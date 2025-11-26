<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Comment
{
    private ?int $id = null;

    public function __construct(
        private int $articleId,
        private int $authorId,
        private string $body,
    ) {
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function markAsCreated(int $id): void
    {
        if ($this->id !== null) {
            throw new \DomainException('Cannot change comment ID.');
        }

        $this->id = $id;
    }
}