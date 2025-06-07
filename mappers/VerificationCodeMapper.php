<?php

declare(strict_types=1);

namespace app\mappers;

use app\core\Mapper;
use app\core\Model;
use app\models\VerificationCode;

class VerificationCodeMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $findByUserIdAndCode;
    private ?\PDOStatement $deleteByUserId;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO verification_codes (user_id, code, expires_at) VALUES (:user_id, :code, :expires_at)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE verification_codes SET user_id = :user_id, code = :code, expires_at = :expires_at WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("
            DELETE FROM verification_codes WHERE id = :id
        ");
        $this->select = $this->getPdo()->prepare("
            SELECT * FROM verification_codes WHERE id = :id
        ");
        $this->selectAll = $this->getPdo()->prepare("
            SELECT * FROM verification_codes
        ");
        $this->findByUserIdAndCode = $this->getPdo()->prepare("
            SELECT * FROM verification_codes WHERE user_id = :user_id AND code = :code AND expires_at > NOW()
        ");
        $this->deleteByUserId = $this->getPdo()->prepare("
            DELETE FROM verification_codes WHERE user_id = :user_id
        ");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var VerificationCode $model */
        $this->insert->execute([
            ':user_id' => $model->getUserId(),
            ':code' => $model->getCode(),
            ':expires_at' => $model->getExpiresAt()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);
        return $model;
    }

    protected function doUpdate(Model $model): void
    {
        /** @var VerificationCode $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':user_id' => $model->getUserId(),
            ':code' => $model->getCode(),
            ':expires_at' => $model->getExpiresAt()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var VerificationCode $model */
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

    public function findByUserIdAndCode(int $userId, string $code): ?VerificationCode
    {
        $this->findByUserIdAndCode->execute([':user_id' => $userId, ':code' => $code]);
        $result = $this->findByUserIdAndCode->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $this->createObject($result) : null;
    }
    
    public function deleteByUserId(int $userId): void
    {
        $this->deleteByUserId->execute([':user_id' => $userId]);
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new VerificationCode(
            id: isset($data['id']) ? (int)$data['id'] : null,
            user_id: (int)$data['user_id'],
            code: $data['code'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at']
        );
    }
}