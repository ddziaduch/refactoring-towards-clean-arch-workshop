<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

final class Comment
{
    public function __construct(
        // uuid
        private string $id,
        private int $articleId,
        private int $authorId,
        private string $body,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
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

    public function getUuid(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}