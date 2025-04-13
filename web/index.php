<?php

declare(strict_types=1);

use app\controllers\PresentationController;
use app\core\Application;

const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT."vendor/autoload.php";



$application = new Application();

$router = $application->getRouter();

$router->setGetRoute("/", [new PresentationController(), "getView"]);
$router->setPostRoute("/handle", [new PresentationController(), "handleView"]);

ob_start();
$application->run();
ob_flush();

