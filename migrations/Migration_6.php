<?php
declare(strict_types=1);
namespace app\migrations;

use app\core\Migration;

class Migration_6 extends Migration
{
    public function getVersion(): int
    {
        return 6;
    }

    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE verification_codes(
            id SERIAL PRIMARY KEY,
            user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
            code VARCHAR(4) NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
        parent::up();
    }
}