<?php

declare(strict_types=1);

namespace Clean\Application\Port\Secondary;

use Clean\Application\Exception\EntityNotFoundException;
use Clean\Domain\Entity\Article;

interface ArticleRepositoryInterface
{
    /**
     * this is used when null it not excepted
     * @throws EntityNotFoundException
     */
    public function getBySlug(string $articleSlug): Article;

    // this is used when null is accepted
    public function findBySlug(string $articleSlug): ?Article;
}