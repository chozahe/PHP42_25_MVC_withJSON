<?php

declare(strict_types=1);

namespace app\core;

class ConfigParser
{
    public static function load()
    {
        $filename = PROJECT_ROOT . ".env";
        if (!file_exists($filename)) return;
        $lines = file($filename);
        foreach ($lines as $line) {
            $dataline = trim(explode("#", $line)[0]);
            if (strlen($dataline)===0) continue;
            $params = explode("=", $dataline, 2);
            if (count($params)!==2) continue;
            $key = rtrim($params[0]);
            $value = ltrim($params[1]);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv("$key=$value");

        }
    }
}