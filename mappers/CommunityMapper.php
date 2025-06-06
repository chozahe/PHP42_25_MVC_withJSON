<?php
declare(strict_types=1);
namespace app\mappers;

use app\core\Mapper;
use app\core\Model;
use app\models\Community;

class CommunityMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO communities (name, description, creator_id)
            VALUES (:name, :description, :creator_id)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE communities
            SET name = :name, description = :description, creator_id = :creator_id
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM communities WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM communities WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM communities");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var Community $model */
        $this->insert->execute([
            ':name' => $model->getName(),
            ':description' => $model->getDescription(),
            ':creator_id' => $model->getCreatorId()
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
        /** @var Community $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':name' => $model->getName(),
            ':description' => $model->getDescription(),
            ':creator_id' => $model->getCreatorId()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var Community $model */
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

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new Community(
            id: isset($data['id']) ? (int)$data['id'] : null,
            name: $data['name'],
            description: $data['description'] ?? null,
            creator_id: isset($data['creator_id']) ? (int)$data['creator_id'] : null
        );
    }
}