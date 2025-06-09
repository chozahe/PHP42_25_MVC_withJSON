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
    private PDOStatement $findByVerificationCode;
    private PDOStatement $deleteUnverified;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO users (username, email, password_hash, first_name, second_name, is_verified, verification_code, code_expires_at)
            VALUES (:username, :email, :password_hash, :first_name, :second_name, :is_verified, :verification_code, :code_expires_at)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE users
            SET username = :username, email = :email, password_hash = :password_hash,
                first_name = :first_name, second_name = :second_name,
                is_verified = :is_verified, verification_code = :verification_code,
                code_expires_at = :code_expires_at
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM users WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM users WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM users");
        $this->selectByUsername = $this->getPdo()->prepare("SELECT * FROM users WHERE username = :username");
        $this->selectByEmail = $this->getPdo()->prepare("SELECT * FROM users WHERE email = :email");
        $this->findByVerificationCode = $this->getPdo()->prepare("
            SELECT * FROM users WHERE id = :user_id AND verification_code = :code
        ");
        $this->deleteUnverified = $this->getPdo()->prepare("
            DELETE FROM users WHERE is_verified = FALSE AND code_expires_at < NOW()
        ");
    }

    public function doInsert(Model $model): Model
    {
        /** @var User $model */
        $this->insert->bindValue(':username', $model->getUsername(), \PDO::PARAM_STR);
        $this->insert->bindValue(':email', $model->getEmail(), \PDO::PARAM_STR);
        $this->insert->bindValue(':password_hash', $model->getPasswordHash(), \PDO::PARAM_STR);
        $this->insert->bindValue(':first_name', $model->getFirstName(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->insert->bindValue(':second_name', $model->getSecondName(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->insert->bindValue(':is_verified', $model->isVerified(), \PDO::PARAM_BOOL);
        $this->insert->bindValue(':verification_code', $model->getVerificationCode(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->insert->bindValue(':code_expires_at', $model->getCodeExpiresAt(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->insert->execute();
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

        $this->update->bindValue(':id', $model->getId(), \PDO::PARAM_INT);
        $this->update->bindValue(':username', $model->getUsername(), \PDO::PARAM_STR);
        $this->update->bindValue(':email', $model->getEmail(), \PDO::PARAM_STR);
        $this->update->bindValue(':password_hash', $model->getPasswordHash(), \PDO::PARAM_STR);
        $this->update->bindValue(':first_name', $model->getFirstName(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->update->bindValue(':second_name', $model->getSecondName(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->update->bindValue(':is_verified', $model->isVerified(), \PDO::PARAM_BOOL);
        $this->update->bindValue(':verification_code', $model->getVerificationCode(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->update->bindValue(':code_expires_at', $model->getCodeExpiresAt(), \PDO::PARAM_STR | \PDO::PARAM_NULL);
        $this->update->execute();

        
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

    public function findByVerificationCode(int $userId, string $code): ?User
    {
        
    $this->findByVerificationCode->bindValue(':user_id', $userId, \PDO::PARAM_INT);
    $this->findByVerificationCode->bindValue(':code', $code, \PDO::PARAM_STR);
    $this->findByVerificationCode->execute();
    
    $result = $this->findByVerificationCode->fetch(\PDO::FETCH_NAMED);
    return $result !== false ? $this->createObject($result) : null;
    }

    public function deleteUnverified(): void
    {
        $this->deleteUnverified->execute();
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): User
    {
        return new User(
            id: isset($data['id']) ? (int)$data['id'] : null,
            username: $data['username'],
            email: $data['email'],
            password_hash: $data['password_hash'],
            first_name: $data['first_name'] ?? null,
            second_name: $data['second_name'] ?? null,
            is_verified: isset($data['is_verified']) ? (bool)$data['is_verified'] : false,
            verification_code: $data['verification_code'] ?? null,
            code_expires_at: $data['code_expires_at'] ?? null
        );
    }
}