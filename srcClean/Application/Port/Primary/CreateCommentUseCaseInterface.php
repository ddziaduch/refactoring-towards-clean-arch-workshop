<?php
declare(strict_types=1);

namespace Clean\Application\Port\Primary;

use App\Entity\User;
use Clean\Application\Exception\EntityNotFoundException;
use Clean\Domain\Entity\Comment;

interface CreateCommentUseCaseInterface
{
    /**
     * @throws EntityNotFoundException when the article does not exist
     */
    public function createArticleComment(string $articleSlug, User $user, string $commentBody): Comment;
}