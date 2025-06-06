<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    /**
     * Проверяет JWT-токен в запросе
     * @throws ValidationException
     */
    public function handle(Request $request): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            throw new ValidationException(
                'Authorization header with Bearer token is required',
                HttpStatusCodeEnum::HTTP_UNAUTHORIZED->value
            );
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], $_ENV['JWT_ALGORITHM']));
            $request->setJwtData((array)$decoded);
            Application::$app->getLogger()->info("JWT verified for user_id: {$decoded->user_id}");
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("JWT verification failed: " . $e->getMessage());
            throw new ValidationException(
                "Invalid or expired JWT token: " . $e->getMessage(),
                HttpStatusCodeEnum::HTTP_UNAUTHORIZED->value
            );
        }
    }
}