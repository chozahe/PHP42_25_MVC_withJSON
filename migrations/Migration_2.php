<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_2 extends \app\core\Migration
{

    public function getVersion(): int
    {
        return 2;
    }

    function up(): void
    {
        $this->database->pdo->query("ALTER TABLE users ALTER COLUMN phone  TYPE varchar(50)");

        parent::up();
    }
}