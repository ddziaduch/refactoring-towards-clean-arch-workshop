<?php

namespace App\ArticleMgmt\Infrastructure;

use App\ArticleMgmt\Domain\ArticleAlreadyExists;
use App\ArticleMgmt\Domain\ArticleDoesNotExist;
use App\ArticleMgmt\Domain\Entity\Article;
use App\ArticleMgmt\Domain\ArticleRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineArticleRepository implements ArticleRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function store(Article $article): void
    {
        try {
            $this->entityManager->persist($article);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            throw new ArticleAlreadyExists(
                sprintf('Article with slug %s already exists.', $article->slug),
                previous: $exception,
            );
        }
    }

    public function has(Article $article): bool
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->count(['slug' => $article->slug]) === 1;
    }

    public function getBySlug(string $slug): Article
    {
        return $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['slug' => $slug]);
    }

    public function deleteBySlug(string $slug, int $authorId): void
    {
        $numberOfDeletedRows = $this->entityManager
            ->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->delete()
            ->where('a.slug = :slug AND a.author = :authorId')
            ->setParameter('slug', $slug)
            ->setParameter('authorId', $authorId)
            ->getQuery()
            ->execute();

        if ($numberOfDeletedRows === 0) {
            throw new ArticleDoesNotExist(sprintf('Article with slug %s does not exist or you are not the author.', $slug));
        }
    }
}