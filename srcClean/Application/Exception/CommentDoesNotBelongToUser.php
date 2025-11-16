<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

final class CommentDoesNotBelongToUser extends \RuntimeException
{
    public static function withCommentIdAndUserId(int $commentId, int $userId): self
    {
        return new self(
            sprintf(
                'Comment with id %d does not belong to user with id %d',
                $commentId,
                $userId,
            ),
        );
    }
}