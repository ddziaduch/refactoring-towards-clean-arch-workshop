<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

use DateTimeImmutable;

final readonly class CommentReadModel
{
    public function __construct(
        public int $id,
        public string $body,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
    }
}