<?php
declare(strict_types=1);
namespace app\models;
use app\core\Model;

class RefreshToken extends Model
{
    private int $user_id;
    private string $token;
    private ?string $created_at;
    private string $expires_at;

    public function __construct(
        ?int $id,
        int $user_id,
        string $token,
        string $expires_at
    ) {
        parent::__construct($id);
        $this->user_id = $user_id;
        $this->token = $token;
        $this->created_at = null;
        $this->expires_at = $expires_at;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getExpiresAt(): string
    {
        return $this->expires_at;
    }

    public function setExpiresAt(string $expires_at): void
    {
        $this->expires_at = $expires_at;
    }
}