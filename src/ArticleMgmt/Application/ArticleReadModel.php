<?php

namespace App\ArticleMgmt\Application;

final readonly class ArticleReadModel
{
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public string $body,
        public string $description,
        public int $authorId,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public int $favoritesCount,
        public array $tags,
    ) {
    }
}