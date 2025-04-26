<?php

declare(strict_types=1);

use app\controllers\PresentationController;
use app\core\Application;
use app\core\ConfigParser;

const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT . "vendor/autoload.php";

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
$router->setGetRoute("/error", "");

ob_start();
$application->run();
ob_flush();

