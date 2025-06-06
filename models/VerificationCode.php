<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;

class VerificationCode extends Model
{
    private int $user_id = 0;
    private string $code = '';
    private string $expires_at = '';
    private string $created_at = '';

    public function __construct(
            ?int $id,
            int $user_id,
            string $code,
            string $expires_at,
            string $created_at
        ) {
        parent::__construct($id);
        $this->user_id = $user_id ;
        $this->code = $code; 
        $this->expires_at = $expires_at; 
        $this->created_at = $created_at; 
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getExpiresAt(): string
    {
        return $this->expires_at;
    }

    public function setExpiresAt(string $expires_at): void
    {
        $this->expires_at = $expires_at;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
}