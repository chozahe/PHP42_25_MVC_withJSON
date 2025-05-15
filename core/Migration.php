<?php

declare(strict_types=1);

namespace app\core;

abstract class Migration
{

    protected Database $database;
    public abstract function getVersion(): int;

    public function up(): void
    {
        $this->database->pdo->query("DELETE FROM migrations;");
        $this->database->pdo->query("INSERT INTO migrations (version) values ({$this->getVersion()});");
    }

    public function down():void {}

    /**
     * @param Database $database
     */
    public function setDatabase(Database $database): void
    {
        $this->database = $database;
    }


}