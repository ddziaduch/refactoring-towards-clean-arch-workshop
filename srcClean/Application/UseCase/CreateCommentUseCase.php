<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\ArticleMgmt\Domain\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateCommentUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function create(
        string $articleSlug,
        string $commentBody,
        User $user,
    ): Comment {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw new \RuntimeException('Article not found');
        }

        $commentEntity = new Comment($article, $user, $commentBody);

        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();

        return $commentEntity;
    }
}