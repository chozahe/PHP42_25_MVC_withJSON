<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\RouteException;
use app\exceptions\ValidationException;

class Router
{
    private Request $request;

    private Response $response;

    private array $routes = [];

    private array $protectedRoutes = [];


    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function setGetRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::GET->value][$path] = $callback;
    }

    public function setProtectedRoute(string $method, string $path): void
    {
        $this->protectedRoutes[$method][$path] = true;
    }

    public function setPostRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::POST->value][$path] = $callback;
    }

    public function resolve(): void
    {
        $path = parse_url($this->request->getUri(), PHP_URL_PATH);
        $method = $this->request->getMethod();
        if ($method === MethodEnum::GET && preg_match("/(png|jpe?g|css|js)/", $path)) {
            $this->renderStatic(ltrim($path, "/"));
            return;
        }

        if (!isset($this->routes[$method->value]) || !isset($this->routes[$method->value][$path])) {
            $this->renderStatic("404.html");
            $this->response->setStatusCode(HttpStatusCodeEnum::HTTP_NOT_FOUND);
            return;
        }

        $callback = $this->routes[$method->value][$path];

        try {
            //  middleware для защищённых маршрутов
            if (isset($this->protectedRoutes[$method->value][$path])) {
                $middleware = new JwtMiddleware();
                $middleware->handle($this->request);
            }

            if (is_string($callback)) {
                if (empty($callback)) {
                    throw new RouteException("empty callback");
                }
                $this->renderView($callback);
            } elseif (is_array($callback)) {
                call_user_func($callback, $this->request);
            }
        } catch (ValidationException $e) {
            $this->response->setStatusCode(HttpStatusCodeEnum::from($e->getCode() ?: 400));
            $this->renderJson(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->response->setStatusCode(HttpStatusCodeEnum::HTTP_SERVER_ERROR);
            $this->renderJson(['error' => 'Internal server error']);
            Application::$app->getLogger()->error("Cannot resolve route: $e");
        }
    }

    public function renderView(string $name): void
    {
        include PROJECT_ROOT . "views/$name.php";
    }

    public function renderTemplate(string $name, array $data=[]): void
    {

       Template::View($name.'.html', $data);
    }
    public function renderStatic(string $name): void
    {
        include PROJECT_ROOT . "web/$name";
    }
    private function renderJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
