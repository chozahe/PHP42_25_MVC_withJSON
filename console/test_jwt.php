<?php
const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT . "vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Настройки JWT из конфигурации (предполагаем, что они есть в JsonConfigLoader)
$secretKey = 'your_jwt_secret'; // Замени на свой секретный ключ из конфига
$algorithm = 'HS256'; // Алгоритм подписи
$issuedAt = time();
$expire = $issuedAt + 3600; // Токен действителен 1 час

// Данные для JWT (payload)
$payload = [
    'iss' => 'http://example.org', // Издатель токена
    'aud' => 'http://example.com', // Получатель токена
    'iat' => $issuedAt, // Время выпуска
    'exp' => $expire, // Время истечения
    'user_id' => 1, // Пример данных пользователя
    'username' => 'testuser'
];

try {
    // Генерируем JWT
    $jwt = JWT::encode($payload, $secretKey, $algorithm);
    echo "Generated JWT: $jwt\n";

    // Декодируем JWT для проверки
    $decoded = JWT::decode($jwt, new Key($secretKey, $algorithm));
    echo "Decoded JWT:\n";
    print_r($decoded);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}