<?php

declare(strict_types=1);

namespace app\controllers\api;

use app\core\Application;
use app\core\HttpStatusCodeEnum;
use app\core\Request;
use app\exceptions\ValidationException;
use app\services\AuthService;
use app\utilits\JsonRenderable;

class AuthController
{
    use JsonRenderable;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * POST /api/register
     */
    public function register(Request $request): void
    {
        try {
            $body = $request->getJsonBody();
            $user = $this->authService->register($body);
            $this->renderJson([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'first_name' => $user->getFirstName(),
                    'second_name' => $user->getSecondName(),
                    'created_at' => $user->getCreatedAt()
                ]
            ], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            Application::$app->getLogger()->error("Registration failed: " . $e->getMessage());
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during registration: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }

    /**
     * POST /api/login
     */
    public function login(Request $request): void
    {
        try {
            $body = $request->getJsonBody();
            $tokens = $this->authService->login($body);
            $this->renderJson([
                'message' => 'Login successful',
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $tokens['expires_in']
            ], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            Application::$app->getLogger()->error("Login failed: " . $e->getMessage());
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during login: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }

    /**
     * POST /api/refresh
     */
    public function refresh(Request $request): void
    {
        try {
            $body = $request->getJsonBody();
            if (!isset($body['refresh_token'])) {
                throw new ValidationException("Refresh token is required");
            }
            $tokens = $this->authService->refreshToken($body['refresh_token']);
            $this->renderJson([
                'message' => 'Token refreshed successfully',
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_in' => $tokens['expires_in']
            ], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            Application::$app->getLogger()->error("Token refresh failed: " . $e->getMessage());
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during token refresh: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): void
    {
        try {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
                throw new ValidationException("Authorization header with Bearer token is required");
            }
            $jwt = $matches[1];
            $this->authService->logout($jwt);
            $this->renderJson(['message' => 'Logout successful'], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            Application::$app->getLogger()->error("Logout failed: " . $e->getMessage());
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during logout: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }

    /**
     * POST /api/me
     */
    public function getCurrentUser(Request $request): void
    {
        try {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
                throw new ValidationException("Authorization header with Bearer token is required", HttpStatusCodeEnum::HTTP_UNAUTHORIZED->value);
            }
            $jwt = $matches[1];
            $user = $this->authService->getCurrentUser($jwt);
            if (!$user) {
                throw new ValidationException("User not found", HttpStatusCodeEnum::HTTP_NOT_FOUND->value);
            }
            $this->renderJson([
                'message' => 'User profile retrieved successfully',
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'first_name' => $user->getFirstName(),
                    'second_name' => $user->getSecondName(),
                    'created_at' => $user->getCreatedAt()
                ]
            ], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            Application::$app->getLogger()->error("Get user profile failed: " . $e->getMessage());
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::tryFrom($e->getCode()) ?? HttpStatusCodeEnum::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during get user profile: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }

    /**
     * POST /api/verify-code
     */
    public function verifyCode(Request $request): void
    {
        try {
            $body = $request->getJsonBody();
            if (!isset($body['user_id'], $body['code'])) {
                throw new ValidationException("User ID and code are required", HttpStatusCodeEnum::HTTP_BAD_REQUEST->value);
            }
            $isValid = $this->authService->verifyCode((int)$body['user_id'], $body['code']);
            if (!$isValid) {
                throw new ValidationException("Invalid or expired verification code", HttpStatusCodeEnum::HTTP_BAD_REQUEST->value);
            }
            $this->renderJson(['message' => 'Verification successful'], HttpStatusCodeEnum::HTTP_OK);
        } catch (ValidationException $e) {
            $this->renderJson(['error' => $e->getMessage()], HttpStatusCodeEnum::tryFrom($e->getCode()) ?? HttpStatusCodeEnum::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Unexpected error during verification: " . $e->getMessage());
            $this->renderJson(['error' => 'Internal server error'], HttpStatusCodeEnum::HTTP_SERVER_ERROR);
        }
    }
}