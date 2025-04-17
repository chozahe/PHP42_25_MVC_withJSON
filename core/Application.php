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

    public function run() {

        try {
            $this->router->resolve();
        }
        catch (\Exception $exception) {
            var_dump($exception);
            $this->response->setStatusCode(HttpStatusCodeEnum::HTTP_SERVER_ERROR);
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