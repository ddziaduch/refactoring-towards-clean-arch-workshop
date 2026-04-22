<?php

namespace Clean\Application\Ports\Secondary;

use App\Entity\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $commentEntity): void;
}
