<?php
declare(strict_types=1);
namespace app\models;
use app\core\Model;

class User extends Model
{
    private string $username;
    private string $email;
    private string $password_hash;
    private ?string $first_name;
    private ?string $second_name;
    private ?string $created_at;
    private bool $is_verified;
    private ?string $verification_code;
    private ?string $code_expires_at;

    public function __construct(
        string $username,
        string $email,
        string $password_hash,
        ?string $first_name = null,
        ?string $second_name = null,
        bool $is_verified = false,
        ?string $verification_code = null,
        ?string $code_expires_at = null,
        ?int $id = null
    ) {
        parent::__construct($id);
        $this->username = $username;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->created_at = null;
        $this->is_verified = $is_verified;
        $this->verification_code = $verification_code;
        $this->code_expires_at = $code_expires_at;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function setPasswordHash(string $password_hash): void
    {
        $this->password_hash = $password_hash;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): void
    {
        $this->first_name = $first_name;
    }

    public function getSecondName(): ?string
    {
        return $this->second_name;
    }

    public function setSecondName(?string $second_name): void
    {
        $this->second_name = $second_name;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function isVerified(): bool
    {
        return $this->is_verified ?? false; 
    }
    public function setIsVerified(bool $is_verified): void 
    {
         $this->is_verified = $is_verified; 
    }
    public function getVerificationCode(): ?string 
    {
        return $this->verification_code; 
    }
    public function setVerificationCode(?string $code): void 
    { 
        $this->verification_code = $code; 
    }
    public function getCodeExpiresAt(): ?string 
    { 
        return $this->code_expires_at; 
    }
    public function setCodeExpiresAt(?string $expires_at): void 
    { 
        $this->code_expires_at = $expires_at; 
    }
}