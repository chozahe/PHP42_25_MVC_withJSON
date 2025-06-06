<?php
declare(strict_types=1);
namespace app\mappers;
use app\core\Mapper;
use app\core\Model;
use app\models\User;
use PDOStatement;

class UserMapper extends Mapper
{
    private PDOStatement $insert;
    private PDOStatement $update;
    private PDOStatement $delete;
    private PDOStatement $select;
    private PDOStatement $selectAll;
    private PDOStatement $selectByUsername;
    private PDOStatement $selectByEmail;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO users (username, email, password_hash, first_name, second_name)
            VALUES (:username, :email, :password_hash, :first_name, :second_name)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE users
            SET username = :username, email = :email, password_hash = :password_hash,
                first_name = :first_name, second_name = :second_name
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM users WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM users WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM users");
        $this->selectByUsername = $this->getPdo()->prepare("SELECT * FROM users WHERE username = :username");
        $this->selectByEmail = $this->getPdo()->prepare("SELECT * FROM users WHERE email = :email");
    }

    public function doInsert(Model $model): Model
    {
        /** @var User $model */
        $this->insert->execute([
            ':username' => $model->getUsername(),
            ':email' => $model->getEmail(),
            ':password_hash' => $model->getPasswordHash(),
            ':first_name' => $model->getFirstName(),
            ':second_name' => $model->getSecondName()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);

        $this->select->execute([':id' => $id]);
        $data = $this->select->fetch(\PDO::FETCH_NAMED);
        if ($data !== false) {
            $model->setCreatedAt($data['created_at']);
        }

        return $model;
    }

    public function doUpdate(Model $model): void
    {
        /** @var User $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':username' => $model->getUsername(),
            ':email' => $model->getEmail(),
            ':password_hash' => $model->getPasswordHash(),
            ':first_name' => $model->getFirstName(),
            ':second_name' => $model->getSecondName()
        ]);
    }

    public function doDelete(Model $model): void
    {
        /** @var User $model */
        $this->delete->execute([':id' => $model->getId()]);
    }

    public function doSelect(int $id): array
    {
        $this->select->execute([':id' => $id]);
        $result = $this->select->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $result : [];
    }

    public function doSelectAll(): array
    {
        $this->selectAll->execute();
        return $this->selectAll->fetchAll(\PDO::FETCH_NAMED);
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function doSelectByUsername(string $username): ?array
{
    $this->selectByUsername->execute([':username' => $username]);
    $result = $this->selectByUsername->fetch(\PDO::FETCH_NAMED);
    return $result !== false ? $result : null;
}

    public function doSelectByEmail(string $email): ?array
{
    $this->selectByEmail->execute([':email' => $email]);
    $result = $this->selectByEmail->fetch(\PDO::FETCH_NAMED);
    return $result !== false ? $result : null;
}


    public function createObject(array $data): User
    {
        return new User(
            id: isset($data['id']) ? (int)$data['id'] : null,
            username: $data['username'],
            email: $data['email'],
            password_hash: $data['password_hash'],
            first_name: $data['first_name'] ?? null,
            second_name: $data['second_name'] ?? null
        );
    }
}