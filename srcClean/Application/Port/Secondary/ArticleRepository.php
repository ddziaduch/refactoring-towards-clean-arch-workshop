<?php

declare(strict_types=1);

namespace Clean\Application\Port\Secondary;

use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Domain\Entity\Article;

interface ArticleRepository
{

    public function findBySlug(string $articleSlug): ?Article;

    /**
     * @throws ArticleNotFoundException
     */
    public function getBySlug(string $articleSlug): Article;
}