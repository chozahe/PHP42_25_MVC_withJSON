<?php

declare(strict_types=1);

namespace app\services;

use app\core\Application;
use app\core\Model;
use app\exceptions\ValidationException;
use app\mappers\RefreshTokenMapper;
use app\mappers\UserMapper;
use app\models\RefreshToken;
use app\models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService
{
    private UserMapper $userMapper;
    private RefreshTokenMapper $refreshTokenMapper;

    public function __construct()
    {
        $this->userMapper = new UserMapper();
        $this->refreshTokenMapper = new RefreshTokenMapper();
    }

    /**
     * Регистрация нового пользователя
     * @throws ValidationException
     */
    public function register(array $data): User
    {
        $requiredFields = ['username', 'email', 'password'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                throw new ValidationException("Field '$field' is required");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }

        if (strlen($data['password']) < 8) {
            throw new ValidationException("Password must be at least 8 characters long");
        }

        $existingUser = $this->userMapper->doSelectByUsername($data['username']);
        if ($existingUser) {
            throw new ValidationException("Username already exists");
        }
        $existingEmail = $this->userMapper->doSelectByEmail($data['email']);
        if ($existingEmail) {
            throw new ValidationException("Email already exists");
        }

        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

        $user = new User(
            id: null,
            username: $data['username'],
            email: $data['email'],
            password_hash: $passwordHash,
            first_name: $data['first_name'] ?? null,
            second_name: $data['second_name'] ?? null
        );

        // Сохранение в базе
        $this->userMapper->Insert($user);

        return $user;
    }

    /**
     * @throws ValidationException
     */
    public function login(array $data): array
    {
        // Валидация
        if (!isset($data['username']) || !isset($data['password'])) {
            throw new ValidationException("Username and password are required");
        }

        $userData = $this->userMapper->doSelectByUsername($data['username']);
        if (!$userData) {
            throw new ValidationException("Invalid username or password");
        }

        $user = $this->userMapper->createObject($userData);

        if (!password_verify($data['password'], $user->getPasswordHash())) {
            throw new ValidationException("Invalid username or password");
        }

        // Генерация JWT
        $issuedAt = time();
        $jwtPayload = [
            'iss' => 'http://example.org',
            'aud' => 'http://example.com',
            'iat' => $issuedAt,
            'exp' => $issuedAt + (int)$_ENV['JWT_TOKEN_TTL'],
            'user_id' => $user->getId(),
            'username' => $user->getUsername()
        ];

        $jwt = JWT::encode($jwtPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);

        $refreshTokenValue = bin2hex(random_bytes(32));
        $refreshTokenExpiresAt = date('Y-m-d H:i:s', $issuedAt + (int)$_ENV['JWT_REFRESH_TOKEN_TTL']);

        $refreshToken = new RefreshToken(
            id: null,
            user_id: $user->getId(),
            token: $refreshTokenValue,
            expires_at: $refreshTokenExpiresAt
        );

        $this->refreshTokenMapper->Insert($refreshToken);

        return [
            'access_token' => $jwt,
            'refresh_token' => $refreshTokenValue,
            'expires_in' => (int)$_ENV['JWT_TOKEN_TTL']
        ];
    }

    /**
     * @throws ValidationException
     */
    public function refreshToken(string $refreshToken): array
    {
        // Поиск рефреш-токена
        $tokenData = $this->refreshTokenMapper->findByToken($refreshToken);
        if (!$tokenData) {
            throw new ValidationException("Invalid or expired refresh token");
        }

        $refreshTokenModel = $this->refreshTokenMapper->createObject($tokenData);
        $userData = $this->userMapper->doSelect($refreshTokenModel->getUserId());
        if (!$userData) {
            throw new ValidationException("User not found");
        }

        $user = $this->userMapper->createObject($userData);

        // Генерация нового JWT
        $issuedAt = time();
        $jwtPayload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + (int)$_ENV['JWT_TOKEN_TTL'],
            'user_id' => $user->getId(),
            'username' => $user->getUsername()
        ];

        $jwt = JWT::encode($jwtPayload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']);

        $newRefreshTokenValue = bin2hex(random_bytes(32));
        $newRefreshTokenExpiresAt = date('Y-m-d H:i:s', $issuedAt + (int)$_ENV['JWT_REFRESH_TOKEN_TTL']);

        $newRefreshToken = new RefreshToken(
            id: null,
            user_id: $user->getId(),
            token: $newRefreshTokenValue,
            expires_at: $newRefreshTokenExpiresAt
        );

        $this->refreshTokenMapper->deleteByUserId($user->getId());
        $this->refreshTokenMapper->Insert($newRefreshToken);

        return [
            'access_token' => $jwt,
            'refresh_token' => $newRefreshTokenValue,
            'expires_in' => (int)$_ENV['JWT_TOKEN_TTL']
        ];
    }

    /**
     * Выход пользователя
     * @throws ValidationException
     */
    public function logout(string $jwt): void
    {
        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $userId = $decoded->user_id;

            // Удаление рефреш-токена
            $this->refreshTokenMapper->deleteByUserId($userId);

        } catch (\Exception $e) {
            throw new ValidationException("Invalid JWT token: " . $e->getMessage());
        }
    }

    /**
     * @throws ValidationException
     */
    public function getCurrentUser(string $jwt): ?User
    {
        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $userData = $this->userMapper->doSelect($decoded->user_id);
            if (!$userData) {
                return null;
            }
            return $this->userMapper->createObject($userData);
        } catch (\Exception $e) {
            throw new ValidationException("Invalid JWT token: " . $e->getMessage());
        }
    }
}