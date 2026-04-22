<?php

namespace Clean\Adapters\Secondary;

use App\Entity\Comment;
use Clean\Application\Ports\Secondary\CommentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function save(Comment $commentEntity): void
    {
        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();
    }
}
