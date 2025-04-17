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

    
    //почитал, что &_POST не парсит jsonки так что отдельни метод напистаь нада
    public function getJsonBody(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        return is_array($data) ? $data : [];
    }

    
}
