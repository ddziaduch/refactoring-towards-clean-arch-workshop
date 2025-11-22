<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use App\Entity\User;
use Clean\Application\Exception\UserNotFound;

interface UserRepository
{
    /**
     * @throws UserNotFound
     */
    public function getById(int $userId): User;
}