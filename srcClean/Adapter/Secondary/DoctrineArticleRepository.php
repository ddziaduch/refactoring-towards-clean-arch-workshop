<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\Port\Secondary\ArticleRepositoryInterface;
use Clean\Domain\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function getBySlug(string $articleSlug): Article
    {
        $doctrineEntity = $this->entityManager->getRepository(\Clean\Infrastructure\DoctrineEntity\Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$doctrineEntity) {
            throw new EntityNotFoundException('Article not found');
        }

        return new Article($doctrineEntity->id);
    }

    public function findBySlug(string $articleSlug): ?Article
    {
        $doctrineEntity = $this->entityManager->getRepository(\Clean\Infrastructure\DoctrineEntity\Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$doctrineEntity) {
            return null;
        }

        return new Article($doctrineEntity->id);
    }
}