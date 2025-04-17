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
