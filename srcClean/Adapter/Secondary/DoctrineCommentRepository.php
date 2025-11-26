<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use App\Entity\Article;
use App\Entity\User;
use Clean\Domain\Entity\Comment;
use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineCommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function store(Comment $domainEntity): void
    {
        $doctrineEntity = new \App\Entity\Comment(
            $this->entityManager->find(Article::class, $domainEntity->getArticleId()),
            $this->entityManager->find(User::class, $domainEntity->getAuthorId()),
            $domainEntity->getBody(),
        );

        $this->entityManager->persist($doctrineEntity);
        $this->entityManager->flush();

        $domainEntity->markAsCreated($doctrineEntity->id());
    }
}