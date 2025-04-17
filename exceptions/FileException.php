<?php

declare(strict_types=1);

namespace app\exceptions;

class FileException extends \Exception
{
   private string $filename;

   public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, string $filename="")
   {
       parent::__construct($message, $code, $previous);
       $this->filename = $filename;
   }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}