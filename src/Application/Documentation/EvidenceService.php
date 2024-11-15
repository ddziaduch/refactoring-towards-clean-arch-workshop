<?php

namespace App\Application\Documentation;

use App\Domain\Documentation\Evidence;
use App\Domain\Documentation\EvidenceRepositoryInterface;

class EvidenceService
{
    private EvidenceRepositoryInterface $repository;

    public function __construct(EvidenceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function addEvidence(string $title, string $description, \DateTimeImmutable $dateCollected): Evidence
    {
        $evidence = new Evidence($title, $description, $dateCollected);
        $this->repository->save($evidence);
        return $evidence;
    }

    public function getEvidenceById(int $id): ?Evidence
    {
        return $this->repository->findById($id);
    }

    public function listAllEvidence(): array
    {
        return $this->repository->findAll();
    }
}
