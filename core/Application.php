<?php

declare(strict_types=1);

namespace app\core;

class Application
{
    public static Application $app;
    private Request $request;
    private Response $response;
    private Router $router;

    public function __construct()
    {
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }


    //если uri начинается с /api, то вызываем специальный метод для обработки json, иначе как обычно вроде ничего ломаться не должно
    public function run(): void
    {
            $this->router->resolve();

    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
