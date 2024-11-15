<?php

namespace App\Infrastructure\Documentation;

use App\Domain\Documentation\Evidence;
use App\Domain\Documentation\EvidenceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineEvidenceRepository implements EvidenceRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(EvidenceEntity::class);
    }

    public function save(Evidence $evidence): void
    {
        $entity = new EvidenceEntity();
        $entity->setTitle($evidence->getTitle());
        $entity->setDescription($evidence->getDescription());
        $entity->setDateCollected($evidence->getDateCollected());

        if ($evidence->getId() !== null) {
            $entity->setId($evidence->getId());
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        if ($evidence->getId() === null) {
            $evidence->setId($entity->getId());
        }
    }

    public function findById(int $id): ?Evidence
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            return null;
        }

        return new Evidence(
            $entity->getTitle(),
            $entity->getDescription(),
            $entity->getDateCollected()
        );
    }

    public function findAll(): array
    {
        $entities = $this->repository->findAll();
        return array_map(function (EvidenceEntity $entity) {
            return new Evidence(
                $entity->getTitle(),
                $entity->getDescription(),
                $entity->getDateCollected()
            );
        }, $entities);
    }
}
