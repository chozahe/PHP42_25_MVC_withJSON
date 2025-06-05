<?php
declare(strict_types=1);
namespace app\mappers;
use app\core\Mapper;
use app\core\Model;
use app\models\Comment;

class CommentMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $findByPostId;
    private ?\PDOStatement $findByUserId;
    private ?\PDOStatement $findByParentId;
    private ?\PDOStatement $findTopLevelByPostId;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO comments (post_id, user_id, parent_id, content)
            VALUES (:post_id, :user_id, :parent_id, :content)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE comments
            SET post_id = :post_id, user_id = :user_id, parent_id = :parent_id,
                content = :content, is_deleted = :is_deleted
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM comments WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM comments WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("
            SELECT c.* FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.is_deleted = FALSE AND p.is_deleted = FALSE
        ");
        $this->findByPostId = $this->getPdo()->prepare("
            SELECT c.* FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.post_id = :post_id AND c.is_deleted = FALSE AND p.is_deleted = FALSE
        ");
        $this->findByUserId = $this->getPdo()->prepare("
            SELECT c.* FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.user_id = :user_id AND c.is_deleted = FALSE AND p.is_deleted = FALSE
        ");
        $this->findByParentId = $this->getPdo()->prepare("
            SELECT c.* FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.parent_id = :parent_id AND c.is_deleted = FALSE AND p.is_deleted = FALSE
        ");
        $this->findTopLevelByPostId = $this->getPdo()->prepare("
            SELECT c.* FROM comments c
            JOIN posts p ON c.post_id = p.id
            WHERE c.post_id = :post_id AND c.parent_id IS NULL AND c.is_deleted = FALSE AND p.is_deleted = FALSE
        ");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var Comment $model */
        $this->insert->execute([
            ':post_id' => $model->getPostId(),
            ':user_id' => $model->getUserId(),
            ':parent_id' => $model->getParentId(),
            ':content' => $model->getContent()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);

        $this->select->execute([':id' => $id]);
        $data = $this->select->fetch(\PDO::FETCH_NAMED);
        if ($data !== false) {
            $model->setCreatedAt($data['created_at']);
            $model->setIsDeleted((bool)$data['is_deleted']);
        }

        return $model;
    }

    protected function doUpdate(Model $model): void
    {
        /** @var Comment $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':post_id' => $model->getPostId(),
            ':user_id' => $model->getUserId(),
            ':parent_id' => $model->getParentId(),
            ':content' => $model->getContent(),
            ':is_deleted' => $model->isDeleted()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var Comment $model */
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

    public function findByUserId(int $user_id): array
    {
        $this->findByUserId->execute([':user_id' => $user_id]);
        return $this->findByUserId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByParentId(int $parent_id): array
    {
        $this->findByParentId->execute([':parent_id' => $parent_id]);
        return $this->findByParentId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findTopLevelByPostId(int $post_id): array
    {
        $this->findTopLevelByPostId->execute([':post_id' => $post_id]);
        return $this->findTopLevelByPostId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new Comment(
            id: isset($data['id']) ? (int)$data['id'] : null,
            post_id: (int)$data['post_id'],
            user_id: isset($data['user_id']) ? (int)$data['user_id'] : null,
            parent_id: isset($data['parent_id']) ? (int)$data['parent_id'] : null,
            content: $data['content'],
            is_deleted: isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false
        );
    }
}