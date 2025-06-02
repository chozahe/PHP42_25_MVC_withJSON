<?php

declare(strict_types=1);

namespace app\core;

abstract class Model
{
    private ?int $id;

    public function __construct(?int $id)
    {
        if (!is_null($id)) {
            $this->setId($id);
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}