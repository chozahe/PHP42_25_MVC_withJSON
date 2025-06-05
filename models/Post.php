<?php
declare(strict_types=1);
namespace app\models;
use app\core\Model;

class Post extends Model
{
    private int $community_id;
    private ?int $user_id;
    private string $title;
    private string $content;
    private ?string $created_at;
    private ?string $updated_at;
    private bool $is_deleted;

    public function __construct(
        ?int $id,
        int $community_id,
        ?int $user_id,
        string $title,
        string $content,
        ?string $updated_at = null,
        bool $is_deleted = false
    ) {
        parent::__construct($id);
        $this->community_id = $community_id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->content = $content;
        $this->created_at = null;
        $this->updated_at = $updated_at;
        $this->is_deleted = $is_deleted;
    }

    public function getCommunityId(): int
    {
        return $this->community_id;
    }

    public function setCommunityId(int $community_id): void
    {
        $this->community_id = $community_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }
}