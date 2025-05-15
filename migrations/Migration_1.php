<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_1 extends \app\core\Migration
{

    public function getVersion(): int
    {
        return 1;
    }

    public function up(): void
    {
        $this->database->pdo->query("ALTER TABLE users ADD COLUMN phone varchar(15)");

        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("ALTER TABLE users DROP COLUMN phone");
    }
}