<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use App\Entity\Article;
use Clean\Application\Exception\ArticleNotFound;

interface ArticleRepository
{
    /**
     * @throws ArticleNotFound
     */
    public function getBySlug(string $articleSlug): Article;
}