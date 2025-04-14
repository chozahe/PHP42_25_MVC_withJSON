<?php
//просто чтобы проверить работоспособность моих неких новвоведений

namespace app\controllers\api;

use app\core\Application;

class ApiController
{
    public function hello()
    {
        return ['message' => 'привет json!'];
    }

    public function helloUser(): array
    {
        $body = Application::$app->getRequest()->getJsonBody();

        $username = $body['username'] ?? 'гость';

        return [
            'message' => 'Привет, ' . $username . '!'
        ];
    }
}
