<?php

declare(strict_types=1);

namespace app\models;

use app\core\Model;

class User extends Model
{

 //["id", "first_name", "second_name", "email", "job", "age"];

    private string $first_name;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getSecondName(): string
    {
        return $this->second_name;
    }

    /**
     * @param string $second_name
     */
    public function setSecondName(string $second_name): void
    {
        $this->second_name = $second_name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * @param string $job
     */
    public function setJob(string $job): void
    {
        $this->job = $job;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    private  string $second_name;

    private string $email;

    private string $job;

    private  int $age;

    /**
     * @param string $first_name
     * @param string $second_name
     * @param string $email
     * @param string $job
     * @param int $age
     */
    public function __construct(?int $id, string $first_name, string $second_name, string $email, string $job, int $age)
    {
        parent::__construct($id);
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->email = $email;
        $this->job = $job;
        $this->age = $age;
    }


}