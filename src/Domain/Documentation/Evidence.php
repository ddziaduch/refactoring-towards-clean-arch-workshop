<?php

namespace App\Domain\Documentation;

class Evidence
{
    private ?int $id = null;
    private string $title;
    private string $description;
    private \DateTimeImmutable $dateCollected;

    public function __construct(string $title, string $description, \DateTimeImmutable $dateCollected)
    {
        $this->title = $title;
        $this->description = $description;
        $this->dateCollected = $dateCollected;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDateCollected(): \DateTimeImmutable
    {
        return $this->dateCollected;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
