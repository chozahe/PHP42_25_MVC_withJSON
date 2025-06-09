<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    /**
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
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("JWT verification failed: " . $e->getMessage());
            throw new ValidationException(
                "Invalid or expired JWT token: " . $e->getMessage(),
                HttpStatusCodeEnum::HTTP_UNAUTHORIZED->value
            );
        }
    }
}