<?php
declare(strict_types=1);
namespace app\mappers;
use app\core\Mapper;
use app\core\Model;
use app\models\Post;

class PostMapper extends Mapper
{
    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;
    private ?\PDOStatement $findByCommunityId;
    private ?\PDOStatement $findByUserId;
    private ?\PDOStatement $findByTags;
    private ?\PDOStatement $findByTagsAndCommunity;

    public function __construct()
    {
        parent::__construct();
        $this->insert = $this->getPdo()->prepare("
            INSERT INTO posts (community_id, user_id, title, content)
            VALUES (:community_id, :user_id, :title, :content)
        ");
        $this->update = $this->getPdo()->prepare("
            UPDATE posts
            SET community_id = :community_id, user_id = :user_id, title = :title,
                content = :content, updated_at = CURRENT_TIMESTAMP, is_deleted = :is_deleted
            WHERE id = :id
        ");
        $this->delete = $this->getPdo()->prepare("DELETE FROM posts WHERE id = :id");
        $this->select = $this->getPdo()->prepare("SELECT * FROM posts WHERE id = :id");
        $this->selectAll = $this->getPdo()->prepare("SELECT * FROM posts WHERE is_deleted = FALSE");
        $this->findByCommunityId = $this->getPdo()->prepare("
            SELECT * FROM posts WHERE community_id = :community_id AND is_deleted = FALSE
        ");
        $this->findByUserId = $this->getPdo()->prepare("
            SELECT * FROM posts WHERE user_id = :user_id AND is_deleted = FALSE
        ");
        $this->findByTags = $this->getPdo()->prepare("
            SELECT p.* FROM posts p
            JOIN post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = ANY(:tag_ids) AND p.is_deleted = FALSE
            GROUP BY p.id
            HAVING COUNT(DISTINCT pt.tag_id) = :tag_count
        ");
        $this->findByTagsAndCommunity = $this->getPdo()->prepare("
            SELECT p.* FROM posts p
            JOIN post_tags pt ON p.id = pt.post_id
            WHERE pt.tag_id = ANY(:tag_ids) AND p.community_id = :community_id AND p.is_deleted = FALSE
            GROUP BY p.id
            HAVING COUNT(DISTINCT pt.tag_id) = :tag_count
        ");
    }

    protected function doInsert(Model $model): Model
    {
        /** @var Post $model */
        $this->insert->execute([
            ':community_id' => $model->getCommunityId(),
            ':user_id' => $model->getUserId(),
            ':title' => $model->getTitle(),
            ':content' => $model->getContent()
        ]);
        $id = $this->getPdo()->lastInsertId();
        $model->setId((int)$id);

        $this->select->execute([':id' => $id]);
        $data = $this->select->fetch(\PDO::FETCH_NAMED);
        if ($data !== false) {
            $model->setCreatedAt($data['created_at']);
            $model->setUpdatedAt($data['updated_at']);
            $model->setIsDeleted((bool)$data['is_deleted']);
        }

        return $model;
    }

    protected function doUpdate(Model $model): void
    {
        /** @var Post $model */
        $this->update->execute([
            ':id' => $model->getId(),
            ':community_id' => $model->getCommunityId(),
            ':user_id' => $model->getUserId(),
            ':title' => $model->getTitle(),
            ':content' => $model->getContent(),
            ':is_deleted' => $model->isDeleted()
        ]);
    }

    protected function doDelete(Model $model): void
    {
        /** @var Post $model */
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

    public function findByCommunityId(int $community_id): array
    {
        $this->findByCommunityId->execute([':community_id' => $community_id]);
        return $this->findByCommunityId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByUserId(int $user_id): array
    {
        $this->findByUserId->execute([':user_id' => $user_id]);
        return $this->findByUserId->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByTags(array $tag_ids): array
    {
        $this->findByTags->execute([
            ':tag_ids' => '{' . implode(',', $tag_ids) . '}',
            ':tag_count' => count($tag_ids)
        ]);
        return $this->findByTags->fetchAll(\PDO::FETCH_NAMED);
    }

    public function findByTagsAndCommunity(array $tag_ids, int $community_id): array
    {
        $this->findByTagsAndCommunity->execute([
            ':tag_ids' => '{' . implode(',', $tag_ids) . '}',
            ':community_id' => $community_id,
            ':tag_count' => count($tag_ids)
        ]);
        return $this->findByTagsAndCommunity->fetchAll(\PDO::FETCH_NAMED);
    }

    public function getInstance(): Mapper
    {
        return $this;
    }

    public function createObject(array $data): Model
    {
        return new Post(
            id: isset($data['id']) ? (int)$data['id'] : null,
            community_id: (int)$data['community_id'],
            user_id: isset($data['user_id']) ? (int)$data['user_id'] : null,
            title: $data['title'],
            content: $data['content'],
            updated_at: $data['updated_at'] ?? null,
            is_deleted: isset($data['is_deleted']) ? (bool)$data['is_deleted'] : false
        );
    }
}