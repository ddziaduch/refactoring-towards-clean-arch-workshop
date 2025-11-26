<?php
declare(strict_types=1);

namespace Clean\Application\Port\Secondary;

use Clean\Domain\Entity\Comment;

interface CommentRepositoryInterface
{
    public function store(Comment $domainEntity): void;
}