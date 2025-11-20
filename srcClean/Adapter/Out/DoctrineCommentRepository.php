<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Entity\Comment;
use Clean\Application\Port\Out\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCommentRepository implements CommentRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Comment $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }
}