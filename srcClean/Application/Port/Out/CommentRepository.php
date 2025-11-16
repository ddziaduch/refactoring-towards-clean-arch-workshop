<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\Exception\CommentNotFound;
use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function save(Comment $comment): void;

    /**
     * @throws CommentNotFound
     */
    public function getById(int $commentId): Comment;

    public function delete(Comment $comment): void;
}