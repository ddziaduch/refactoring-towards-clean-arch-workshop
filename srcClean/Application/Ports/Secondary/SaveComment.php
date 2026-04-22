<?php

namespace Clean\Application\Ports\Secondary;

use Clean\Domain\Entities\Comment;

interface SaveComment
{
    public function save(Comment $commentEntity): void;
}
