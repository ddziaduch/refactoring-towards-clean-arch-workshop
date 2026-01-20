<?php

declare(strict_types=1);

namespace Clean\Adapter\Secondary;

use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Domain\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineArticleRepository implements ArticleRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function findBySlug(string $articleSlug): ?Article
    {
        return $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);
    }

    public function getBySlug(string $articleSlug): Article
    {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw new ArticleNotFoundException('Article not found');
        }

        return $article;
    }
}