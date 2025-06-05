<?php
declare(strict_types=1);
namespace app\models;
use app\core\Model;

class Tag extends Model
{
    private string $name;

    public function __construct(?int $id, string $name)
    {
        parent::__construct($id);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}