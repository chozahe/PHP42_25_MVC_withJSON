<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;

class Comment extends Model
{
    private int $post_id;
    private ?int $user_id;
    private ?int $parent_id;
    private string $content;
    private ?string $created_at;
    private bool $is_deleted;
    

    public function __construct(
        ?int $id,
        int $post_id,
        ?int $user_id,
        ?int $parent_id,
        string $content,
        bool $is_deleted = false
    ) {
        parent::__construct($id);
        $this->post_id = $post_id;
        $this->user_id = $user_id;
        $this->parent_id = $parent_id;
        $this->content = $content;
        $this->created_at = null;
        $this->is_deleted = $is_deleted;
    }

    public function getPostId(): int
    {
        return $this->post_id;
    }

    public function setPostId(int $post_id): void
    {
        $this->post_id = $post_id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function setParentId(?int $parent_id): void
    {
        $this->parent_id = $parent_id;
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

    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): void
    {
        $this->is_deleted = $is_deleted;
    }
    
}
