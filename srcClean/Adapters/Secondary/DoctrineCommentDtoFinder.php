<?php

namespace Clean\Adapters\Secondary;

use Clean\Application\Dtos\CommentDto;
use Clean\Application\Ports\Secondary\CommentDtoFinder;
use Clean\Domain\Entities\Comment;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineCommentDtoFinder implements CommentDtoFinder
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findById(int $commentId): ?CommentDto
    {
        $entity = $this->entityManager->find(Comment::class, $commentId);

        return new CommentDto(
            $entity->id(),
            $entity->body,
            $entity->createdAt,
            $entity->updatedAt,
        );
    }
}
