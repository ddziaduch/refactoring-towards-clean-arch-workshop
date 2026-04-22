<?php

namespace Clean\Application\Ports\Secondary;

use Clean\Domain\Entities\Comment;

interface CommentRepositoryInterface
{
    public function save(Comment $commentEntity): void;
}
