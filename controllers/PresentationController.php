<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\Application;
use app\exceptions\FileException;

class PresentationController
{
  public function getView() {
      Application::$app->getRouter()->renderView("presentation");
  }

  public function handleView() {
      $body = Application::$app->getRequest()->getBody();
      $filename = PROJECT_ROOT ."runtime/". "dump.txt";
      $f = @fopen($filename, "a");
      if ($f === false) {
          throw new FileException("can not open file", filename: $filename );
      }
      foreach ($body as $key=>$value) {
         if (!fwrite($f, "$key=>$value".PHP_EOL)) {
             throw new FileException("can not write to file", filename: $filename );
         }
      }
      fclose($f);

  }
}