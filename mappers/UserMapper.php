<?php

declare(strict_types=1);

namespace app\mappers;

use app\core\Mapper;
use app\core\Model;
use app\models\User;

class UserMapper extends \app\core\Mapper
{


    private ?\PDOStatement $insert;
    private ?\PDOStatement $update;
    private ?\PDOStatement $delete;
    private ?\PDOStatement $select;
    private ?\PDOStatement $selectAll;

    //"id", "first_name", "second_name", "email", "job", "age"];
    public function __construct()
    {
        parent::__construct();

        $this->insert = $this->getPdo()->prepare("
            INSERT INTO users (first_name, second_name, email, job, age) VALUES (:first_name, :second_name, :email, :job, :age)
        ");

        $this->update = $this->getPdo()->prepare("
            UPDATE users 
                SET first_name = :first_name,
                    second_name = :second_name,
                    email = :email,
                    job = :job,
                    age = :age  
                WHERE id = :id
                    
            ");

        $this->delete = $this->getPdo()->prepare("DELETE FROM users WHERE id = :id");

        $this->select = $this->getPdo()->prepare("SELECT * FROM users WHERE id = :id");

        $this->select = $this->getPdo()->prepare("SELECT * FROM users");

    }

    /**
     * @param User $model
     * @return Model
     */
    protected function doInsert(Model $model): Model
    {

       $this->update->execute([
           //(first_name, second_name, email, job, age)
           ":first_name" => $model->getFirstName(),
           ":second_name" => $model->getSecondName(),
           ":email" => $model->getEmail(),
           ":job" =>$model->getJob(),
           ":age" =>$model->getAge()
       ]);

       $id = $this->getPdo()->lastInsertId();
       $model->setId((int)$id);
       return $model;
    }

    /**
     * @param User $model
     * @return void
     */
    protected function doUpdate(Model $model)
    {
        $this->update->execute([
            ":id" => $model->getId(),
            ":first_name" => $model->getFirstName(),
           ":second_name" => $model->getSecondName(),
           ":email" => $model->getEmail(),
           ":job" =>$model->getJob(),
           ":age" =>$model->getAge()]);
    }

    protected function doDelete(Model $model)
    {
       $this->delete->execute([":id"=>$model->getId()]);
    }

    protected function doSelect(int $id): array
    {
        $this->select->execute([":id"=>$id]);
       return $this->select->fetch(\PDO::FETCH_NAMED);
    }

    protected function doSelectAll(): array
    {
        $this->selectAll->execute();
        return $this->selectAll->fetchAll(\PDO::FETCH_NAMED);
    }

    public function getInstance(): Mapper
    {
       return $this;
    }

    public function createObject(array $data): Model
    {
       return new User(
           id: (array_key_exists("id", $data) ? $data["data"] : null),
           first_name: $data["first_name"],
           second_name: $data["second_name"],
           email: $data["email"],
           job: $data["job"],
           age: $data["age"]
       );
    }
}