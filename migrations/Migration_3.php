<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_3 extends \app\core\Migration{
    
    public function getVersion(): int
    {
        return 3;
    }

    public function up(): void
    {
        $this->database->pdo->query("
            CREATE TABLE tags (
                id SERIAL PRIMARY KEY,
                name VARCHAR(50) NOT NULL UNIQUE
            );
        ");

        $this->database->pdo->query("
        CREATE TABLE post_tags (
                post_id INTEGER REFERENCES posts(id) ON DELETE CASCADE,
                tag_id INTEGER REFERENCES tags(id) ON DELETE CASCADE,
                PRIMARY KEY (post_id, tag_id)
            )
        ");
        parent::up();
    }

    public function down(): void
    {
        $this->database->pdo->query("DROP TABLE post_tags");
        $this->database->pdo->query("DROP TABLE tags");
    }
}