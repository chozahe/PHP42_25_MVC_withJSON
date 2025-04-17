<?php

declare(strict_types=1);

namespace app\core;

enum HttpStatusCodeEnum: int
{
    case HTTP_OK = 200;

    case HTTP_NOT_FOUND = 404;

    case HTTP_SERVER_ERROR = 500;
}
