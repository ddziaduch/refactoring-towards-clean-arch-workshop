<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use App\ArticleMgmt\Domain\ArticleDoesNotExist;
use App\Entity\Comment;
use App\Entity\User;

interface CreateCommentUseCaseInterface
{
    /**
     * @param int|null $userId
     * @throws ArticleDoesNotExist
     */
    public function create(string $articleSlug, string $commentBody, ?int $userId): Comment;
}