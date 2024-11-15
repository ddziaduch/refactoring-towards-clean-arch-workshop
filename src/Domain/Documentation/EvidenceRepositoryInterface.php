<?php

namespace App\Domain\Documentation;

interface EvidenceRepositoryInterface
{
    public function save(Evidence $evidence): void;

    public function findById(int $id): ?Evidence;

    public function findAll(): array;
}
