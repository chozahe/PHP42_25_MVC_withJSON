<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;

class User extends Model
{

    public function getTableName(): string
    {
      return "users";
    }

    public function getAttributes(): array
    {
        return ["id", "first_name", "second_name", "email", "job", "age"];
    }
}