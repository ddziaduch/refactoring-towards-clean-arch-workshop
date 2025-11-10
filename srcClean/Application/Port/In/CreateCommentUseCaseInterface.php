<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use Clean\Application\Exception\ArticleNotFound;

interface CreateCommentUseCaseInterface
{
    /**
     * @throws ArticleNotFound
     */
    public function create(string $articleSlug, string $commentBody, int $userId): int;
}