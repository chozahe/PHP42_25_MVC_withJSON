<?php

namespace app\utilits;

trait JsonRenderable
{
    public function renderJson(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
