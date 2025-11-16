<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

final class CommentDoesNotBelongToArticle extends \RuntimeException
{
    public static function withCommentIdAndArticleSlug(int $commentId, string $articleSlug): self
    {
        return new self(
            sprintf(
                'Comment with id %d does not belong to article with slug %s',
                $commentId,
                $articleSlug,
            ),
        );
    }
}