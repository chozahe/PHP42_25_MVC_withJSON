<?php

declare(strict_types=1);

use app\controllers\PresentationController;
use app\core\Application;
use app\controllers\api\ApiController;

const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT . "vendor/autoload.php";



$application = new Application();

$router = $application->getRouter();

$router->setGetRoute("/", [new PresentationController(), "getView"]);
$router->setPostRoute("/handle", [new PresentationController(), "handleView"]);
$router->setGetRoute("/api/helloApi", [new ApiController(), "hello"]);
$router->setPostRoute("/api/helloApi", [new ApiController(), "helloUser"]);
$router->setGetRoute("/error", "");

ob_start();
$application->run();
ob_flush();
