<?php
declare(strict_types=1);

namespace Clean\Application\Port\Primary;

use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\ReadModel\CommentReadModel;

interface CreateCommentUseCaseInterface
{
    /**
     * @throws EntityNotFoundException when the article does not exist
     */
    public function createArticleComment(string $articleSlug, int $authorId, string $commentBody): CommentReadModel;
}