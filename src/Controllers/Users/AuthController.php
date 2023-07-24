<?php

namespace Controllers\Users;

use System\Base\Controller as BaseController;
use System\Contracts\IValidator;
use System\Contracts\ILogger;
use System\Logger\MonologLogger;
use System\Session;
use Models\Users\Index as UsersModel;
use Models\Session\Index as SessionModel;
use Controllers\Users\Validator\RespectValidator;
use \PDOException;

class AuthController extends BaseController
{
    protected UsersModel $usersModel;
    protected SessionModel $sessionModel;
    protected IValidator $validator;
    protected ILogger $logger;

    public function __construct()
    {
        parent::__construct();
        $this->usersModel   = UsersModel::getInstance();
        $this->sessionModel = SessionModel::getInstance();
        $this->validator    = new RespectValidator();
        $this->logger       = new MonologLogger(static::class);
    }

    public function login()
    {
        $errors = [];
        $fields = [];

        if ($this->env['server']['REQUEST_METHOD'] === 'POST') {
            $fields     = static::getConcreteFields([
                'email',
                'password'
            ], $this->env['post']);
            $isRemember = isset($this->env['post']['isRemember']);

            if (!$this->validator->validateLogin($fields)) {
                $errors = $this->validator->getErrors();
                $this->logger->info("invalid login");
            } else {
                $user = $this->usersModel->getByEmail($fields['email']);

                if ($user === null || !password_verify($fields['password'], $user['password_hash'])) {
                    $errors['common'][] = 'Неверный email или пароль';
                    $this->logger->info("pair login-password not found");
                } else {
                    $this->setUser($user, $isRemember);
                    $this->logger->debug("successfully entered", ['useremail' => $user['email']]);
                    header("Location: " . BASE_URL);
                    exit();
                }
            }
        }

        $this->title   = "Вход";
        $this->content = $this->view->render(
            'Users/login.twig',
            [
                'errors' => $errors,
                'fields' => $fields
            ]
        );
    }

    public function register()
    {
        $errors = [];
        $fields = [];

        if ($this->env['server']['REQUEST_METHOD'] == 'POST') {
            $fields     = static::getConcreteFields([
                'email',
                'password',
                'confirmPassword',
                'firstname',
                'surname',
                'patronymic'
            ], $this->env['post']);
            $isRemember = isset($this->env['post']['isRemember']);

            if (!$this->validator->validateRegistration($fields)) {
                $errors = $this->validator->getErrors();
                $this->logger->info('invalid registration', ['useremail' => $fields['email']]);
            } else {
                try {
                    $idUser = $this->usersModel->add([
                        'email' => $fields['email'],
                        'password_hash' => password_hash($fields['password'], PASSWORD_BCRYPT),
                        'firstName' => $fields['firstname'],
                        'surname' => $fields['surname'],
                        'patronymic' => $fields['patronymic']
                    ]);
                    $user   = $this->usersModel->get($idUser);
                    $this->logger->debug(
                        "user added to database",
                        ['useremail' => $user['email']]
                    );
                    $this->setUser($user, $isRemember);
                    header('Location: ' . BASE_URL);
                } catch (PDOException $ex) {
                    if ($ex->getCode() == '23000') {
                        $errors['common'][] = "Пользователь с таким email уже зарегистрирован";
                        $this->logger->debug("email already taken");
                    } else {
                        $this->logger->error("unexpected PDOException", $ex->getTrace());
                        throw $ex;
                    }
                }
            }
        }

        $this->title   = "Регистрация";
        $this->content = $this->view->render(
            'Users/register.twig',
            [
                'errors' => $errors,
                'fields' => $fields
            ]
        );
    }

    public function logout()
    {
        if ($this->user !== null) {
            $this->logger->debug("user logged out", ['useremail' => $this->user['email']]);
            $this->unsetSessionUser();
        }
        header('Location:' . BASE_URL);
    }

    private function setUser(array $user, bool $isRemember)
    {
        $token = static::generateToken();
        Session::set('token', $token);
        $this->sessionModel->add(['id_user' => $user['id_user'], 'token' => $token]);
        $this->user = $user;
        if ($isRemember) {
            setcookie('token', $token, time() + 3600 * 24 * 30, BASE_URL);
        }
    }

    private static function generateToken(int $length = 64): string
    {
        return substr(bin2hex(random_bytes($length)), $length);
    }

    private static function getConcreteFields(array $fieldNames, array $source)
    {
        $result = [];

        foreach ($fieldNames as $fieldName) {
            $result[$fieldName] = $source[$fieldName];
        }

        return $result;
    }

    public static function getUser(): ?array
    {
        $token = Session::get('token') ?? $_COOKIE['token'] ?? null;

        if ($token == null) {
            return null;
        }

        $sessionModel = SessionModel::getInstance();
        $session      = $sessionModel->getByToken($token);

        if ($session === null || $session['status'] > 0) {
            static::unsetSessionUser();
            return null;
        }

        return UsersModel::getInstance()->get($session['id_user']) ?? null;
    }

    private static function unsetSessionUser()
    {
        $token        = Session::slice('token');
        $sessionModel = SessionModel::getInstance();
        $session      = $sessionModel->getByToken($token);
        $sessionModel->remove($session['id_session']);
        if (isset($_COOKIE['token'])) {
            setcookie('token', '', 0, BASE_URL);
        }
    }
}