<?php

declare(strict_types=1);

namespace app\migrations;

class Migration_0 extends \app\core\Migration
{

    public function getVersion(): int
    {
        return 0;
    }

    function up(): void
    {
        $this->database->pdo->query("CREATE TABLE IF NOT EXISTS users
        (
            id serial primary key ,
            first_name character varying(100)  NOT NULL,
            second_name character varying(100)  NOT NULL,
            age integer,
            job text,
            email text
        );");

        parent::up();
    }
}