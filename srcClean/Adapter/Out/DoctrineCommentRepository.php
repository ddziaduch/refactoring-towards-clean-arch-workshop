<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use Clean\Application\Exception\CommentNotFound;
use Clean\Domain\Entity\Comment;
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

    public function getById(int $commentId): Comment
    {
        return $this->entityManager->find(Comment::class, $commentId)
            ?? throw CommentNotFound::withId($commentId);
    }
}