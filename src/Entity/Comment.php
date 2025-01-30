<?php

namespace App\Entity;

use App\ArticleMgmt\Domain\Entity\Article;
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
    public readonly \DateTimeImmutable $updatedAt;

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
}