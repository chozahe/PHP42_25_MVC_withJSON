<?php
declare(strict_types=1);

namespace app\core;

use Exception;

class JsonConfigLoader
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function load(): void
    {
        if (!file_exists($this->filePath)) {
            throw new Exception("JSON config file not found: {$this->filePath}");
        }

        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in config: " . json_last_error_msg());
        }

        if (isset($data['logs']['path'])) {
            $logPath = rtrim($data['logs']['path'], "/") . "/";
            putenv("APP_LOG_PATH=$logPath");
            $_ENV["APP_LOG_PATH"] = $logPath;
        } else {
            throw new Exception("Missing 'logs.path' in config");
        }

        if (isset($data['jwt'])) {
            $jwt = $data['jwt'];

            putenv("JWT_SECRET={$jwt['secret']}");
            $_ENV["JWT_SECRET"] = $jwt['secret'];

            putenv("JWT_ALGORITHM={$jwt['algorithm']}");
            $_ENV["JWT_ALGORITHM"] = $jwt['algorithm'];

            putenv("JWT_TOKEN_TTL={$jwt['token_ttl']}");
            $_ENV["JWT_TOKEN_TTL"] = $jwt['token_ttl'];

            putenv("JWT_REFRESH_TOKEN_TTL={$jwt['refresh_token_ttl']}");
            $_ENV["JWT_REFRESH_TOKEN_TTL"] = $jwt['refresh_token_ttl'];
        }

        if (isset($data['email'])) {
        $email = $data['email'];
        putenv("SMTP_HOST={$email['smtp_host']}");
        $_ENV["SMTP_HOST"] = $email['smtp_host'];
        putenv("SMTP_PORT={$email['smtp_port']}");
        $_ENV["SMTP_PORT"] = $email['smtp_port'];
        putenv("SMTP_USERNAME={$email['smtp_username']}");
        $_ENV["SMTP_USERNAME"] = $email['smtp_username'];
        putenv("SMTP_PASSWORD={$email['smtp_password']}");
        $_ENV["SMTP_PASSWORD"] = $email['smtp_password'];
        putenv("FROM_EMAIL={$email['from_email']}");
        $_ENV["FROM_EMAIL"] = $email['from_email'];
        putenv("FROM_NAME={$email['from_name']}");
        $_ENV["FROM_NAME"] = $email['from_name'];
    }

    }

    
}