<?php

declare(strict_types=1);

namespace Clean\Application;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CreateArticleCommentUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function __invoke(
        string $articleSlug,
        User $user,
        string $commentBody,
    ): Comment {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        $commentEntity = new Comment($article, $user, $commentBody);
        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();

        return $commentEntity;
    }
}