<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_0 extends \app\core\Migration
{

    public function getVersion(): int
    {
        return 0;
    }

    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                second_name VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("DROP TABLE users");
    }
}