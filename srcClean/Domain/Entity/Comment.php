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
}