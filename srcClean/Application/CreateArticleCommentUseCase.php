<?php

declare(strict_types=1);

namespace Clean\Application;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Port\Secondary\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CreateArticleCommentUseCase
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommentRepository $commentRepository,
    ) {
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
        $this->commentRepository->save($commentEntity);

        return $commentEntity;
    }
}