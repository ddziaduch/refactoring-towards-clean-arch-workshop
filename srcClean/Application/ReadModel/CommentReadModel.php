<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

use Clean\Domain\Entity\Comment;

final readonly class CommentReadModel
{
    public function __construct(
        public string $id,
        public string $body,
        public int $authorId,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromDomainEntity(Comment $comment): self
    {
        return new self(
            $comment->getUuid(),
            $comment->getBody(),
            $comment->getAuthorId(),
            $comment->getCreatedAt(),
            $comment->getUpdatedAt(),
        );
    }
}