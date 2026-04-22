<?php

namespace Clean\Application\Ports\Primary;

use App\Entity\User;
use Clean\Application\Exceptions\ArticleNotFoundException;

/**
 * This is optional in terms of clean architecture, it just allows easier to test the controllers
 */
interface CreateArticleCommentUseCaseInterface
{
    /**
     * @throws ArticleNotFoundException
     */
    public function run(
        string $slug,
        string $commentBody,
        User $user,
    ): int;
}
