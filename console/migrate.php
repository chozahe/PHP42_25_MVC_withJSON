<?php

declare(strict_types=1);

use app\core\ConfigParser;
use app\core\Database;

const PROJECT_ROOT = __DIR__ . "/../";

chdir(PROJECT_ROOT);

require PROJECT_ROOT . "vendor/autoload.php";

include 'migrations/AllMigrations.php';
$migrations = getMigrations();

$colorBlue   = "\033[1;34m";
$colorYellow = "\033[1;33m";
$colorGreen  = "\033[0;32m";
$colorReset  = "\033[0m";

echo $colorBlue . "Starting database migrations..." . PHP_EOL;
echo $colorBlue . "
     /\_/\  (
    ( ^.^ ) _)
      \"/  (
     ( | | )
    (__d b__)    
    " . PHP_EOL . $colorReset;

echo $colorYellow . sprintf("%s migrations found%s", count($migrations), PHP_EOL) . $colorReset;

ConfigParser::load();

$database = new Database(getenv("DB_DSN"), getenv("DB_USER"), getenv("DB_PASSWORD"));

$database->pdo->query("CREATE TABLE if not exists migrations (version int);");
$database->pdo->query("INSERT INTO migrations (version) values (-1);");

$maxver = $database->pdo->query("SELECT max(version) FROM migrations")->fetch(PDO::FETCH_NUM)[0];
echo $colorYellow . sprintf("Current migration: %s%s", $maxver, PHP_EOL) . $colorReset;

foreach ($migrations as $migration) {
    /** @var \app\core\Migration $migration */

    if ($migration->getVersion() <= $maxver) continue;
    $migration->setDatabase($database);
    echo $colorBlue . sprintf("Applying migration %s...%s", $migration->getVersion(), PHP_EOL) . $colorReset;
    $migration->up();
    echo $colorGreen . sprintf("Migration %s applied successfully!%s", $migration->getVersion(), PHP_EOL) . $colorReset;
}

echo $colorGreen . "
       |\_/|        
  ____/ o o \\     
/~____  =ø= /     
(______)__m_m)    
" . PHP_EOL;
echo $colorGreen . "All migrations have been applied successfully!" . $colorReset . PHP_EOL;
