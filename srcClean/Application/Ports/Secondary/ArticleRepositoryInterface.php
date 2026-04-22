<?php

namespace Clean\Application\Ports\Secondary;

use App\Entity\Article;
use Clean\Application\Exceptions\ArticleNotFoundException;

interface ArticleRepositoryInterface
{
    /**
     * @throws ArticleNotFoundException
     */
    public function getOneBySlug(string $slug): Article;

    public function findOneBySlug(string $slug): ?Article;
}
