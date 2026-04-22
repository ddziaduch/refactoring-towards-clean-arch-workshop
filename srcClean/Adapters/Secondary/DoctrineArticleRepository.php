<?php

namespace Clean\Adapters\Secondary;

use Clean\Domain\Entities\Article;
use Clean\Application\Exceptions\ArticleNotFoundException;
use Clean\Application\Ports\Secondary\ArticleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @inheritDoc
     */
    public function getOneBySlug(string $slug): Article
    {
        return $this->findOneBySlug($slug) ?? throw new ArticleNotFoundException('Article not found');
    }

    public function findOneBySlug(string $slug): ?Article
    {
        return $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);
    }
}
