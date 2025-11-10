<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

final class ArticleNotFound extends \RuntimeException
{
    public static function withArticleId(int $id): self
    {
        return new self(sprintf('Article with id %d not found', $id));
    }

    public static function withSlug(string $articleSlug)
    {
        return new self(sprintf('Article with slug %s not found', $articleSlug));
    }
}