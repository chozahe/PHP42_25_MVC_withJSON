<?php
declare(strict_types=1);
namespace app\models;
use app\core\Model;

class Community extends Model
{
    private string $name;
    private ?string $description;
    private ?int $creator_id;
    private ?string $created_at;

    public function __construct(
        ?int $id,
        string $name,
        ?string $description,
        ?int $creator_id
    ) {
        parent::__construct($id);
        $this->name = $name;
        $this->description = $description;
        $this->creator_id = $creator_id;
        $this->created_at = null;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatorId(): ?int
    {
        return $this->creator_id;
    }

    public function setCreatorId(?int $creator_id): void
    {
        $this->creator_id = $creator_id;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }
}
