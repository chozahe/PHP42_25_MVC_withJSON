<?php

declare(strict_types=1);

use app\core\Application;
use app\core\ConfigParser;
use app\core\JsonConfigLoader;
use app\mappers\UserMapper;

require __DIR__ . '/../vendor/autoload.php';

const PROJECT_ROOT = __DIR__ . "/../";

ConfigParser::load();

$application = new Application();

try {
    $userMapper = new UserMapper();
    $userMapper->deleteUnverified();
    echo "Unverified users cleaned successfully\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}