<?php

declare(strict_types=1);

use app\controllers\PresentationController;
use app\core\Application;
use app\core\ConfigParser;
use app\controllers\api\ApiController;
use app\core\JsonConfigLoader;
use app\controllers\api\AuthController;
use app\core\MethodEnum;

const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT . "vendor/autoload.php";

$jsonConfigPath = "config/app.json";
$jsonLoader = new JsonConfigLoader($jsonConfigPath);
$jsonLoader->load();

ConfigParser::load();
if ($_ENV["APP_ENV"] === "dev") {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
    ini_set("log_errors", "1");
    ini_set("error_log", sprintf("%sruntime/%s", PROJECT_ROOT, $_ENV["PHP_LOG"]));
}

$application = new Application();

$router = $application->getRouter();

$router->setGetRoute("/", [new PresentationController(), "getView"]);
$router->setPostRoute("/handle", [new PresentationController(), "handleView"]);
$router->setGetRoute("/api/helloApi", [new ApiController(), "hello"]);
$router->setPostRoute("/api/helloApi", [new ApiController(), "helloUser"]);
$router->setGetRoute("/error", "");
$router->setPostRoute("/api/register", [new AuthController(), "register"]);
$router->setPostRoute("/api/login", [new AuthController(), "login"]);
$router->setPostRoute("/api/refresh", [new AuthController(), "refresh"]);
$router->setPostRoute("/api/logout", [new AuthController(), "logout"]);
$router->setProtectedRoute(MethodEnum::POST->value, "/api/logout");
$router->setGetRoute("/api/me", [new AuthController(), "getCurrentUser"]);
$router->setProtectedRoute(MethodEnum::GET->value, "/api/me");
$router->setPostRoute("/api/verify-code", [new AuthController(), "verifyCode"]);

ob_start();
$application->run();
ob_flush();
