<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Domain\Entity\Comment;
use Clean\Application\Port\Secondary\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCommentRepository implements CommentRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Comment $commentEntity): void
    {
        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();
    }
}