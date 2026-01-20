<?php
declare(strict_types=1);

namespace Clean\Application\Port\Primary;

use Clean\Application\Exception\ArticleNotFoundException;

interface CreateArticleCommentUseCaseInterface
{
    /**
     * @throws ArticleNotFoundException
     *
     * @return int comment ID
     */
    public function __invoke(
        string $articleSlug,
        int $userId,
        string $commentBody,
    ): int;
}