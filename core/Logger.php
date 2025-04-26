<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\FileException;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        $dir = dirname($filename);
        if (!file_exists($dir))
        {
           $success =  mkdir($dir, 0777, true);
           if (!$success) throw new FileException("Cannot create log dir");
        }
    }

    public function log($level, $message, array $context = array()): void
    {
        file_put_contents($this->filename, sprintf("%s\t[$level] $message", date("H-m-s")));
    }
}