<?php

declare(strict_types=1);

namespace app\core;

class Application
{
    public static Application $app;
    private Request $request;
    private Router $router;

    public function __construct()
    {
        self::$app = $this;
        $this->request = new Request();
        $this->router = new Router($this->request);
    }


    //если uri начинается с /api, то вызываем специальный метод для обработки json, иначе как обычно вроде ничего ломаться не должно
    public function run(): void
    {
        $uri = $this->request->getUri();

        if (str_starts_with($uri, '/api')) {
            $this->router->resolveJson();
        } else {
            $this->router->resolve();
        }
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
