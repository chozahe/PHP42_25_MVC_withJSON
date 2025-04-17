<?php

declare(strict_types=1);

namespace app\core;

class Router
{
    private Request $request;

    private array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
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
        if ($method === MethodEnum::GET && preg_match("/(png|jpe?g|css|js)/", $path)) {
            $this->renderStatic(ltrim($path, "/"));
            return;
        }

        if (!isset($this->routes[$method->value]) || !isset($this->routes[$method->value][$path])) {
            $this->renderStatic("404.html");
            http_response_code(404);
            return;
        }

        $callback = $this->routes[$method->value][$path];

        if (is_string($callback)) {
            $this->renderView($callback);
        }

        if (is_array($callback)) {
            call_user_func($callback, $this->request);
        }
    }

    //решил создать отдельные методы для работы с json, т.к. будет нада в моей семестровой
/*     public function resolveJson(): void
    {
        $path = $this->request->getUri();
        $method = $this->request->getMethod();

        if (!isset($this->routes[$method->value]) || !isset($this->routes[$method->value][$path])) {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
            return;
        }

        $callback = $this->routes[$method->value][$path];

        if (is_array($callback)) {
            $result = call_user_func($callback, $this->request);

            //чекнул что лучше всего json encode работает с массивами, так что пусть их принимает пока
            if (is_array($result)) {
                header('Content-Type: application/json');
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Invalid API response']);
            }
            return;
        }
        echo json_encode(['error' => 'Invalid API route']);
    }

 */
    public function renderView(string $name): void
    {
        include PROJECT_ROOT . "views/$name.php";
    }

    public function renderStatic(string $name): void
    {
        include PROJECT_ROOT . "web/$name";
    }

    

}
