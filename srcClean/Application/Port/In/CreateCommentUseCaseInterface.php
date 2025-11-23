<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\UserNotFound;

interface CreateCommentUseCaseInterface
{
    /**
     * @throws ArticleNotFound
     * @throws UserNotFound
     */
    public function create(string $articleSlug, string $commentBody, int $userId): int;
}