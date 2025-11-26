<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Entity\Article;
use App\Entity\User;
use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Clean\Domain\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

final class CreateCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
    ) {
    }

    /**
     * @throws \RuntimeException when the article does not exist
     */
    public function createArticleComment(
        string $articleSlug,
        User $user,
        string $commentBody,
        EntityManagerInterface $entityManager,
    ): Comment {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw new \RuntimeException('Article not found');
        }

        $commentEntity = new Comment($article->id, $user->id, $commentBody);
        $this->commentRepository->store($commentEntity);

        return $commentEntity;
    }
}