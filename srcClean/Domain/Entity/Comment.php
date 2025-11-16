<?php

namespace Clean\Domain\Entity;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Comment
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    private ?int $id = null;
    #[ORM\Column]
    public readonly \DateTimeImmutable $createdAt;
    #[ORM\Column]
    public \DateTimeImmutable $updatedAt;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Article::class)]
        public readonly Article $article,
        #[ORM\ManyToOne(targetEntity: User::class)]
        public readonly User $author,
        #[ORM\Column(type: 'text')]
        public readonly string $body,
    ) {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function markAsDeleted(\DateTimeImmutable $now): void
    {
        $this->updatedAt = $now;
        $this->deletedAt = $now;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}