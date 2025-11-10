<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\ReadModel\CommentDto;

interface CommentReadModel
{
    public function get(int $commentId): CommentDto;
}