<?php

declare(strict_types=1);

namespace Clean\Application\Dto;

final readonly class CommentDto
{
    public function __construct(
        public string $body,
        public \DateTimeInterface $createdAt,
        public \DateTimeInterface $updatedAt,
    ) {}
}