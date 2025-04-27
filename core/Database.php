<?php

declare(strict_types=1);

namespace app\core;

use PDO;
class Database
{
  public PDO $pdo;
  public function __construct(string $dsn, string $user, string $password)
  {
      $this->pdo = new PDO($dsn, $user, $password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
}