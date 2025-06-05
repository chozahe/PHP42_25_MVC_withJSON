<?php 
declare(strict_types=1);
namespace app\migrations;
class Migration_4 extends \app\core\Migration
{
    public function getVersion(): int
    {
        return 4;
    }


    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE comments (
                id SERIAL PRIMARY KEY,
                post_id INTEGER REFERENCES posts(id) ON DELETE CASCADE,
                user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
                parent_comment_id INTEGER REFERENCES comments(id) ON DELETE CASCADE,
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
        $this->database->pdo->query("DROP TABLE comments");
    }
}
