<?php

declare(strict_types=1);

namespace app\core;

enum MethodEnum: string
{
    case GET = "GET";
    case POST = "POST";
    case PUT = "PUT";
    case DELETE = "DELETE";
}
