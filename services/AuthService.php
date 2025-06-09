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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use app\core\Template;


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
        if (!isset($data['username'], $data['email'], $data['password'])) {
            throw new ValidationException("Username, email, and password are required");
        }
        if (strlen($data['password']) < 8) {
            throw new ValidationException("Password must be at least 8 characters long");
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException("Invalid email format");
        }
        if ($this->userMapper->doSelectByUsername($data['username'])) {
            throw new ValidationException("Username already exists");
        }
        if ($this->userMapper->doSelectByEmail($data['email'])) {
            throw new ValidationException("Email already exists");
        }
        $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(
            username: $data['username'],
            email: $data['email'],
            password_hash: $passwordHash,
            first_name: $data['first_name'] ?? null,
            second_name: $data['second_name'] ?? null,
            is_verified: false,
            verification_code: null,
            code_expires_at: null,
            id: null
        );
        $user = $this->userMapper->doInsert($user);
        try {
            $this->sendVerificationCode($user);
        } catch (\Exception $e) {
            Application::$app->getLogger()->error("Failed to send verification email: {$e->getMessage()}");
        }
        return $user;
    }
    /**
     * @throws ValidationException
     */
    public function login(array $data): array
    {

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

        if (!$user->isVerified()) {
        throw new ValidationException("Account not verified");
        }

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

    private function sendEmail(string $to, string $subject, string $body): void
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = (int)$_ENV['SMTP_PORT'];
            $mail->setFrom($_ENV['FROM_EMAIL'], $_ENV['FROM_NAME']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
        } catch (Exception $e) {
            throw new \Exception("Failed to send email");
        }
    }

    public function sendVerificationCode(User $user): void
    {
        $code = sprintf("%04d", random_int(0, 9999));
        $expiresAt = date('Y-m-d H:i:s', time() + 900); 
        $user->setVerificationCode($code);
        $user->setCodeExpiresAt($expiresAt);
        $this->userMapper->doUpdate($user);
        $body = Template::Render('emails/verification_code.html', [
            'username' => $user->getUsername(),
            'code' => $code
        ]);
        $this->sendEmail($user->getEmail(), "Verify Your Account", $body);
    }

    public function verifyCode(int $userId, string $code): bool
    {
        try {
        $user = $this->userMapper->findByVerificationCode($userId, $code);
        if (!$user) {
            return false;
        }

        $user->setIsVerified(true);
        $user->setVerificationCode(null);
        $user->setCodeExpiresAt(null);

        $this->userMapper->doUpdate($user);

        return true;
    } catch (\Throwable $e) {
        Application::$app->getLogger()->error("Четааа с базой данных: " . $e->getMessage());
        return false;
    }

    }
}
