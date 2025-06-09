<?php
declare(strict_types=1);
namespace app\migrations;

use app\migrations\Migration_6;
use app\core\Migration;

class Migration_7 extends Migration
{
    public function getVersion(): int
    {
        return 7;
    }

    public function up(): void
    {
        $migration_6 = new Migration_6();
        $migration_6->setDatabase($this->database);
        $migration_6->down();
            $this->database->pdo->query("
            ALTER TABLE users
            ADD COLUMN is_verified BOOLEAN DEFAULT FALSE,
            ADD COLUMN verification_code VARCHAR(4),
            ADD COLUMN code_expires_at TIMESTAMP
        ");
        parent::up();
    }
}