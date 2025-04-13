<?php

declare(strict_types=1);

namespace app\core;

class Request
{
    public function getUri(): string
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function getMethod(): MethodEnum
    {
        return MethodEnum::from($_SERVER["REQUEST_METHOD"]);
    }

    public function getBody(): array
    {
        $body = [];
        switch ($this->getMethod()) {
            case MethodEnum::GET:
            case MethodEnum::DELETE:
                foreach ($_GET as $key => $value) {
                    $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
                break;
            case MethodEnum::POST:
            case MethodEnum::PUT:
                foreach ($_POST as $key => $value) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
                break;
        }
        return $body;
    }
}