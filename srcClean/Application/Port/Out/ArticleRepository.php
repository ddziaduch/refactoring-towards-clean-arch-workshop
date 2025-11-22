<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Domain\Entity\Article;

interface ArticleRepository
{
    /**
     * @throws ArticleNotFound
     */
    public function getBySlug(string $articleSlug): Article;
}