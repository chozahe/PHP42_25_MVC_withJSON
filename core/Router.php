<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\RouteException;

class Router
{
    private Request $request;

    private Response $response;

    private array $routes = [];


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function setGetRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::GET->value][$path] = $callback;
    }

    public function setPostRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::POST->value][$path] = $callback;
    }

    public function resolve(): void
    {
        $path = $this->request->getUri();
        $method = $this->request->getMethod();
        if ($method === MethodEnum::GET && preg_match("/(png|jpe?g|css|js)/"  ,$path))
        {
            $this->renderStatic(ltrim($path, "/"));
            return;
        }

        if (!isset($this->routes[$method->value]) || !isset($this->routes[$method->value][$path])) {
            $this->renderStatic("404.html");
            $this->response->setStatusCode(HttpStatusCodeEnum::HTTP_NOT_FOUND);
            return;
        }
        $callback = $this->routes[$method->value][$path];

        if (is_string($callback)) {
            if (empty($callback)) {
                throw new RouteException("empty callback");
            }
            $this->renderView($callback);
        }

        if (is_array($callback)) {
            call_user_func($callback, $this->request);
        }
    }

    public function renderView(string $name): void {
        include PROJECT_ROOT."views/$name.php";
    }

    public function renderTemplate(string $name, array $data=[]): void
    {

       Template::View($name.'.html', $data);
    }
    public function renderStatic(string $name): void {
        include PROJECT_ROOT."web/$name";
    }
}