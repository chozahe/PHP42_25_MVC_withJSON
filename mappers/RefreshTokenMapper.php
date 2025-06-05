<?php
declare(strict_types=1);
namespace app\mappers;
use app\core\Mapper;
use app\core\Model;
use app\models\RefreshToken;

class RefreshTokenMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $findByToken;
    private ?\PDOStatement $findByUserId;
    private ?\PDOStatement $deleteByUserId;
    private ?\PDOStatement $deleteExpired;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO refresh_tokens (user_id, token, expires_at)
            VALUES (:user_id, :token, :expires_at)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE refresh_tokens
            SET user_id = :user_id, token = :token, expires_at = :expires_at
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM refresh_tokens WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM refresh_tokens WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("
            SELECT * FROM refresh_tokens WHERE expires_at > NOW()
        ");
        $this->findByToken = $this->getPdo()->prepare("
            SELECT * FROM refresh_tokens
            WHERE token = :token AND expires_at > NOW()
        ");
        $this->findByUserId = $this->getPdo()->prepare("
            SELECT * FROM refresh_tokens
            WHERE user_id = :user_id AND expires_at > NOW()
        ");
        $this->deleteByUserId = $this->getPdo()->prepare("
            DELETE FROM refresh_tokens WHERE user_id = :user_id
        ");
        $this->deleteExpired = $this->getPdo()->prepare("
            DELETE FROM refresh_tokens WHERE expires_at <= NOW()
        ");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var RefreshToken $model */
        $this->deleteByUserId->execute([':user_id' => $model->getUserId()]);

        $this->insert->execute([
            ':user_id' => $model->getUserId(),
            ':token' => $model->getToken(),
            ':expires_at' => $model->getExpiresAt()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);

        // Получаем created_at после вставки
        $this->select->execute([':id' => $id]);
        $data = $this->select->fetch(\PDO::FETCH_NAMED);
        if ($data !== false) {
            $model->setCreatedAt($data['created_at']);
        }

        return $model;
    }

    protected function doUpdate(Model $model): void
    {
        /** @var RefreshToken $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':user_id' => $model->getUserId(),
            ':token' => $model->getToken(),
            ':expires_at' => $model->getExpiresAt()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var RefreshToken $model */
        $this->delete->execute([':id' => $model->getId()]);
    }

    protected function doSelect(int $id): array
    {
        $this->select->execute([':id' => $id]);
        $result = $this->select->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $result : [];
    }

    protected function doSelectAll(): array
    {
        $this->selectAll->execute();
        return $this->selectAll->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByToken(string $token): ?array
    {
        $this->findByToken->execute([':token' => $token]);
        $result = $this->findByToken->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $result : null;
    }

    public function findByUserId(int $user_id): ?array
    {
        $this->findByUserId->execute([':user_id' => $user_id]);
        $result = $this->findByUserId->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $result : null;
    }

    public function deleteByUserId(int $user_id): void
    {
        $this->deleteByUserId->execute([':user_id' => $user_id]);
    }

    public function deleteExpired(): void
    {
        $this->deleteExpired->execute();
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new RefreshToken(
            id: isset($data['id']) ? (int)$data['id'] : null,
            user_id: (int)$data['user_id'],
            token: $data['token'],
            expires_at: $data['expires_at']
        );
    }
}