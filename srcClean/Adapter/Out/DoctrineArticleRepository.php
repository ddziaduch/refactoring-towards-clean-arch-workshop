<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Entity\Article;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Out\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineArticleRepository implements ArticleRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Article $comment): void
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getBySlug(string $articleSlug): Article
    {
        return $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug])
            ?? throw ArticleNotFound::withSlug($articleSlug);
    }
}