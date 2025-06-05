<?php
declare(strict_types=1);
namespace app\migrations;
class Migration_5 extends \app\core\Migration
{
    public function getVersion(): int
    {
        return 5;
    }

    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE refresh_tokens (
                id SERIAL PRIMARY KEY,
                user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                token VARCHAR(255) NOT NULL UNIQUE,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("DROP TABLE refresh_tokens");
    }
}