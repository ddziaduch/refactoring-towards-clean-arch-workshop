<?php

declare(strict_types=1);

namespace Clean\Application\Port\Secondary;

interface UuidGeneratorInterface
{
    public function generate(): string;
}