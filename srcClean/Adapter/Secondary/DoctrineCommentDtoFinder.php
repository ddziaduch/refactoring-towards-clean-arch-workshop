<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Application\Dto\CommentDto;
use Clean\Application\Port\Secondary\CommentDtoFinder;
use Clean\Domain\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCommentDtoFinder implements CommentDtoFinder
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findById(int $commentId): ?CommentDto
    {
        $comment = $this->entityManager->find(Comment::class, $commentId);

        if (!$comment) {
            return null;
        }

        return new CommentDto(
            $comment->body,
            $comment->createdAt,
            $comment->updatedAt,
            $comment
        );
    }
}