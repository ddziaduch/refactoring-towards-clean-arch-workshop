<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use App\Entity\Comment;
use App\Entity\User;

interface CreateCommentUseCaseInterface
{
    public function create(string $articleSlug, string $commentBody, User $user): Comment;
}