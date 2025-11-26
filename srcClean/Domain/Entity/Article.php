<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Article
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}