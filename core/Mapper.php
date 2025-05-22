<?php

declare(strict_types=1);

namespace app\core;



abstract class Mapper
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Application::$app->getDatabase()->pdo;
    }

    public function Insert(Model $model) {
        $this->doInsert($model);
    }

    public function Update(Model $model) {
        $this->doUpdate($model);
    }

    public function Delete(Model $model) {
        $this->doDelete($model);
    }

    public function Select(int $id): Model {
        return $this->createObject($this->doSelect($id));
    }

    public function SelectAll(): Collection {
        return new Collection($this->doSelectAll(), $this->getInstance());
    }

    protected abstract function doInsert(Model $model): Model;

    protected abstract function doUpdate(Model $model);

    protected abstract function doDelete(Model $model);

    protected abstract function doSelect(int $id): array;

    protected abstract function doSelectAll(): array;

    public abstract function getInstance(): Mapper;

    public abstract function createObject(array $data): Model;

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

}