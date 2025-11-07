<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use App\Entity\Comment;

interface CreateCommentUseCaseInterface
{
    /**
     * @throws \RuntimeException
     */
    public function create(string $articleSlug, string $commentBody, int $userId): int;
}