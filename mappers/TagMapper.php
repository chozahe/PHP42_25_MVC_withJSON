<?php
declare(strict_types=1);
namespace app\mappers;
use app\core\Mapper;
use app\core\Model;
use app\models\Tag;

class TagMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $findByPostId;
    private ?\PDOStatement $findByName;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO tags (name) VALUES (:name)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE tags SET name = :name WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("
            DELETE FROM tags WHERE id = :id
        ");
        $this->select = $this->getPdo()->prepare("
            SELECT * FROM tags WHERE id = :id
        ");
        $this->selectAll = $this->getPdo()->prepare("
            SELECT * FROM tags
        ");
        $this->findByPostId = $this->getPdo()->prepare("
            SELECT t.* FROM tags t
            JOIN post_tags pt ON t.id = pt.tag_id
            JOIN posts p ON pt.post_id = p.id
            WHERE pt.post_id = :post_id AND p.is_deleted = FALSE
        ");
        $this->findByName = $this->getPdo()->prepare("
            SELECT * FROM tags WHERE name = :name
        ");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var Tag $model */
        $this->insert->execute([
            ':name' => $model->getName()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);
        return $model;
    }

    protected function doUpdate(Model $model): void
    {
        /** @var Tag $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':name' => $model->getName()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var Tag $model */
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

    public function findByPostId(int $post_id): array
    {
        $this->findByPostId->execute([':post_id' => $post_id]);
        return $this->findByPostId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByName(string $name): ?array
    {
        $this->findByName->execute([':name' => $name]);
        $result = $this->findByName->fetch(\PDO::FETCH_NAMED);
        return $result !== false ? $result : null;
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new Tag(
            id: isset($data['id']) ? (int)$data['id'] : null,
            name: $data['name']
        );
    }
}