<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

final class CommentNotFound extends \RuntimeException
{
    public static function withId(int $commentId): self
    {
        return new self(sprintf('Comment with id %d not found', $commentId));
    }
}