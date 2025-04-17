<?php

namespace app\controllers\api;

use app\core\Application;
use app\utilits\JsonRenderable;

class ApiController
{

    use JsonRenderable;

    public function hello(): void
    {
        $this->renderJson(['message' => 'привет json!']);
    }

    public function helloUser(): void
    {
        $body = Application::$app->getRequest()->getJsonBody();

        $username = $body['username'] ?? 'гость';

        $this->renderJson(['message' => 'Привет, ' . $username . '!']);
    }
}
