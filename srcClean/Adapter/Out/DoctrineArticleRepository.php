<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use Clean\Application\Port\Out\ArticleRepository;
use Clean\Domain\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineArticleRepository implements ArticleRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getBySlug(string $articleSlug): Article
    {
        return $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);
    }
}