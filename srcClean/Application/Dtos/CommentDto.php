<?php

namespace Clean\Application\Dtos;

class CommentDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $body,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
    ) {}
}
