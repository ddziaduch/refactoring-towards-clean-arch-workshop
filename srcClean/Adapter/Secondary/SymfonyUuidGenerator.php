<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Application\Port\Secondary\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

final class SymfonyUuidGenerator implements UuidGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}