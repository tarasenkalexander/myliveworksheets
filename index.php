<?php

use Controllers\Common\Errors\PageNotFoundController;
use System\Exceptions\LoginRequiredException;
use System\Exceptions\InvalidRequestMethodException;//??
use System\Exceptions\PageNotFoundException;
use System\Exceptions\ValidationException;
use System\Logger\LogLevel;
use System\Template;
use System\Router;

include_once("src/Config/init.php");
include_once("vendor/autoload.php");

const BASE_URL = "/my_projects/myliveworksheets/";
const DB_HOST = 'localhost';
const DB_NAME = 'myliveworksheets';
const DB_USER = 'root';
const DB_PASS = '';

try {
    Template::getInstance()->addGlobalVar('baseUrl', BASE_URL);

    $url        = $_SERVER["REQUEST_URI"];
    $router     = new Router(BASE_URL);
    $route      = $router->resolvePath($url);
    $controller = new $route['controller']();
    $controller->setEnvironment($route['params'], $_GET, $_POST, $_SERVER);
    $method = $route['method'];
    $controller->$method();

    $html = $controller->render();
    echo $html;
} catch (PageNotFoundException $ex) {
    http_response_code(404);
    header("Location: " . BASE_URL . "404");
} catch (InvalidRequestMethodException $ex) {
    echo "InvalidRequestMethodException";
} catch (LoginRequiredException $ex) {
    header("Location: " . BASE_URL . "login");
} catch (Exception $ex) {
    echo "Just Exception: " . $ex->getMessage();
}