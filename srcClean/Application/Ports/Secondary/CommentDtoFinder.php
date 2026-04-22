<?php

namespace Clean\Application\Ports\Secondary;

use Clean\Application\Dtos\CommentDto;

interface CommentDtoFinder
{
    public function findById(int $commentId): ?CommentDto;
}
