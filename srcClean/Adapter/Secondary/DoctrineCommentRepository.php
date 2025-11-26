<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Clean\Domain\Entity\Comment;
use Clean\Infrastructure\DoctrineEntity\Article;
use Clean\Infrastructure\DoctrineEntity\User;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function store(Comment $domainEntity): void
    {
        $doctrineEntity = new \Clean\Infrastructure\DoctrineEntity\Comment(
            $this->entityManager->find(Article::class, $domainEntity->getArticleId()),
            $this->entityManager->find(User::class, $domainEntity->getAuthorId()),
            $domainEntity->getBody(),
            $domainEntity->getUuid(),
        );

        $this->entityManager->persist($doctrineEntity);
        $this->entityManager->flush();
    }
}