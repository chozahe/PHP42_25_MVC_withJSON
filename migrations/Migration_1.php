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
        $this->database->pdo->query("
            CREATE TABLE communities (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL UNIQUE,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                creator_id INTEGER REFERENCES users(id) ON DELETE SET NULL
            );
        ");
        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("DROP TABLE communities");
    }
}