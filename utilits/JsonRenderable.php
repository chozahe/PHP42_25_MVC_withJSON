<?php

namespace app\utilits;
use app\core\HttpStatusCodeEnum;

trait JsonRenderable
{
    public function renderJson(array $data, HttpStatusCodeEnum $status = HttpStatusCodeEnum::HTTP_OK): void
    {
        header('Content-Type: application/json');
        http_response_code($status->value);
        echo json_encode($data);
    }
}
