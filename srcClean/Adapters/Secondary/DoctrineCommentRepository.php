<?php

namespace Clean\Adapters\Secondary;

use Clean\Domain\Entities\Comment;
use Clean\Application\Ports\Secondary\CommentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Comment $commentEntity): void
    {
        // log this happen
        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();
    }
}
