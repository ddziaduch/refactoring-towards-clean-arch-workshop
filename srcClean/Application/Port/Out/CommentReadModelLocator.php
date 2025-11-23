<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\ReadModel\CommentReadModel;

interface CommentReadModelLocator
{
    public function get(int $commentId): CommentReadModel;
}