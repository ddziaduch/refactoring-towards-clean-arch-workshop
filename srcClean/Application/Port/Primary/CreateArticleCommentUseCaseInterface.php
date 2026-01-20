<?php
declare(strict_types=1);

namespace Clean\Application\Port\Primary;

use App\Entity\User;
use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Domain\Entity\Comment;

interface CreateArticleCommentUseCaseInterface
{
    /**
     * @throws ArticleNotFoundException
     */
    public function __invoke(
        string $articleSlug,
        User $user,
        string $commentBody,
    ): Comment;
}