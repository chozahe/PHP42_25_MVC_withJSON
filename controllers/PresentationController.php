<?php

declare(strict_types=1);

namespace app\controllers;

use app\core\Application;
use app\core\Logger;
use app\exceptions\FileException;
use app\mappers\UserMapper;
use app\models\User;

class PresentationController
{
  public function getView() {
      Application::$app->getRouter()->renderTemplate("index", ["post_action"=>"handle"]);
  }

  public function handleView() {
      $body = Application::$app->getRequest()->getBody();
      try {
         $mapper = new UserMapper();
         $user = $mapper->createObject($body);
         $mapper->Insert($user);
         $users = $mapper->SelectAll();
         var_dump($users);
      } catch (\PDOException $exception)
      {
          Application::$app->getLogger()->error($exception);

      }

  }
}