<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_2 extends \app\core\Migration
{

    public function getVersion(): int
    {
        return 2;
    }
    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE posts (
                id SERIAL PRIMARY KEY,
                community_id INTEGER REFERENCES communities(id) ON DELETE CASCADE,
                user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP,
                is_deleted BOOLEAN DEFAULT FALSE
            );
        ");
        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("DROP TABLE posts");
    }
}