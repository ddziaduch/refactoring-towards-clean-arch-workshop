<?php

declare(strict_types=1);

namespace Clean\Application\Port\Secondary;

use Clean\Application\Dto\CommentDto;

interface CommentDtoFinder
{
    public function findById(int $commentId): ?CommentDto;
}