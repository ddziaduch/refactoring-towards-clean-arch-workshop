<?php

declare(strict_types=1);

namespace Clean\Application\Exception;

final class UserNotFound extends \RuntimeException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('User with id %d not found', $id));
    }
}