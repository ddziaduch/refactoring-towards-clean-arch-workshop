<?php

namespace Clean\Application\Ports;

use App\Entity\Comment;
use App\Entity\User;

/**
 * This is optional in terms of clean architecture, it just allows easier to test the controllers
 */
interface CreateArticleCommentUseCaseInterface
{
    public function run(
        string $slug,
        string $commentBody,
        User $user,
    ): Comment;
}
