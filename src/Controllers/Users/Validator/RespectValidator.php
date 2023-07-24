<?php
namespace Controllers\Users\Validator;

use System\Contracts\IValidator;
use Respect\Validation\Validator as v;

class RespectValidator implements IValidator
{
    public array $errorsEmail = [];
    public array $errorsPassword = [];
    public array $errorsFirstname = [];
    public array $errorsSurname = [];
    public array $errorsPatronymic = [];
    public array $errorsConfirmPassword = [];

    public function validateLogin(array $fields): bool
    {
        $this->validateEmail($fields['email'])->validatePassword($fields['password']);

        return !$this->hasErrors();
    }

    public function validateRegistration(array $fields): bool
    {
        $this->validateEmail($fields['email'])->
            validatePassword($fields['password'])->
            confirmPassword($fields['password'], $fields['confirmPassword'])->
            validateFirstname($fields['firstname'])->
            validateSurname($fields['surname'])->
            validatePatronymic($fields['patronymic']);

        return !$this->hasErrors();
    }

    public function hasErrors(): bool
    {
        return
            !(empty($this->errorsEmail) &&
                empty($this->errorsPassword) &&
                empty($this->errorsFirstname) &&
                empty($this->errorsSurname) &&
                empty($this->errorsPatronymic) &&
                empty($this->errorsConfirmPassword));
    }

    public function getErrors(): array
    {
        $errors['email']      = $this->errorsEmail;
        $errors['password']   = $this->errorsPassword;
        $errors['firstname']  = $this->errorsFirstname;
        $errors['surname']    = $this->errorsSurname;
        $errors['patronymic'] = $this->errorsPatronymic;
        $errors['confirmPassword']    = $this->errorsConfirmPassword;

        return $errors;
    }

    protected function validateEmail(string $email): static
    {
        if (!v::notEmpty()->validate($email)) {
            $this->errorsEmail[] = "Email не может быть пустым";
        } else if (!v::email()->validate($email)) {
            $this->errorsEmail[] = "Неверный формат email";
        } else if (!v::between(3, 64)->validate(mb_strlen($email))) {
            $this->errorsEmail[] = "Email должен быть от 3 до 64 символов";
        }

        return $this;
    }

    protected function validatePassword(string $password): static
    {
        if (!v::notEmpty()->validate($password)) {
            $this->errorsPassword[] = "Пароль не может быть пустым";
        } else {
            if (!v::between(6, 64)->validate(mb_strlen($password))) {
                $this->errorsPassword[] = "Пароль должен быть от 6 до 64 символов";
            }
            if (!static::validatePasswordSpecialChars($password)) {
                $this->errorsPassword[] = "Разрешены цифры, буквы латинского алфавита и _";
            }
        }

        return $this;
    }

    protected function validateFirstname(string $firstname): static
    {
        if (!v::notEmpty()->validate($firstname)) {
            $this->errorsFirstname[] = "Имя не может быть пустым";
        } else {
            if (!v::between(2, 32)->validate(mb_strlen($firstname))) {
                $this->errorsFirstname[] = "Имя должно быть от 2 до 32 символов";
            }
            if (!static::validateNameSpecialChars($firstname)) {
                $this->errorsFirstname[] = "Разрешены буквы латинского и русского алфавитов, а также -";
            }
        }

        return $this;
    }

    protected function validateSurname(string $surname): static
    {
        if (!v::notEmpty()->validate($surname)) {
            $this->errorsSurname[] = "Фамилия не может быть пустой";
        } else {
            if (!v::between(2, 32)->validate(mb_strlen($surname))) {
                $this->errorsSurname[] = "Фамилия должна быть от 2 до 32 символов";
            }
            if (!static::validateNameSpecialChars($surname)) {
                $this->errorsSurname[] = "Разрешены буквы латинского и русского алфавитов, а также -";
            }
        }

        return $this;
    }

    protected function validatePatronymic(string $patronymic): static
    {
        if (empty($patronymic)) {
            return $this;
        }
        if (!v::between(2, 32)->validate(mb_strlen($patronymic))) {
            $this->errorsPatronymic[] = "Отчество должно быть от 2 до 32 символов";
        }
        if (!static::validateNameSpecialChars($patronymic)) {
            $this->errorsPatronymic[] = "Разрешены буквы латинского и русского алфавитов, а также -";
        }

        return $this;
    }

    private static function validateNameSpecialChars(string $name): bool
    {
        return (bool) preg_match("~[aA-zZаА-яЯ-]+~", $name);
    }

    private static function validatePasswordSpecialChars(string $password): bool
    {
        return (bool) preg_match("~[aA-zZ0-9_]+~", $password);
    }

    public function confirmPassword(string $password, string $passwordToConfirm): static
    {
        if (
            !v::keyValue('confirmPassword', 'equals', 'password')->
                validate(['confirmPassword' => $passwordToConfirm, 'password' => $password])
        ) {
            $this->errorsConfirmPassword[] = "Введённые пароли не совпадают";
        }

        return $this;
    }
}