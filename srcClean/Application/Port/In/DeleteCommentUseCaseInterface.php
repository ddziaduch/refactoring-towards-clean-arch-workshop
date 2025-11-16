<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use Clean\Application\Exception\CommentDoesNotBelongToArticle;
use Clean\Application\Exception\CommentDoesNotBelongToUser;
use Clean\Application\Exception\CommentNotFound;

interface DeleteCommentUseCaseInterface
{
    /**
     * @throws CommentNotFound
     * @throws CommentDoesNotBelongToUser
     * @throws CommentDoesNotBelongToArticle
     */
    public function delete(string $articleSlug, int $commentId, int $userId): void;
}