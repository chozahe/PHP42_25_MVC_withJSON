<?php

declare(strict_types=1);

use app\core\ConfigParser;
use app\core\Database;

const PROJECT_ROOT = __DIR__ . "/../";

chdir(PROJECT_ROOT);

require PROJECT_ROOT . "vendor/autoload.php";

include 'migrations\AllMigrations.php';
$migrations = getMigrations();
echo sprintf("%s migrations found%s", count($migrations), PHP_EOL);

ConfigParser::load();

$database = new Database(getenv("DB_DSN"), getenv("DB_USER"), getenv("DB_PASSWORD"));

$database->pdo->query("CREATE TABLE if not exists migrations (version int);");
$database->pdo->query("INSERT INTO migrations (version) values (-1);");

$maxver = $database->pdo->query("SELECT max(version) FROM migrations")->fetch(PDO::FETCH_NUM)[0];
echo sprintf("Current migration: %s%s", $maxver, PHP_EOL);

foreach ($migrations as $migration) {
    /** @var \app\core\Migration $migration */

    if ($migration->getVersion() <= $maxver) continue;
    $migration->setDatabase($database);
    echo sprintf("Applying migration %s%s", $migration->getVersion(), PHP_EOL);
    $migration->up();

}

