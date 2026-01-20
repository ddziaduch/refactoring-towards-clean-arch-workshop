<?php

declare(strict_types=1);

namespace Clean\Application;

use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Domain\Entity\Comment;
use App\Entity\User;
use Clean\Application\Port\Secondary\CommentRepository;

final class CreateArticleCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ArticleRepository $articleRepository,
    ) {
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function __invoke(
        string $articleSlug,
        User $user,
        string $commentBody,
    ): Comment {
        $article = $this->articleRepository->findBySlug($articleSlug);

        if (!$article) {
            throw new ArticleNotFoundException('Article not found');
        }

        $commentEntity = new Comment($article, $user, $commentBody);
        $this->commentRepository->save($commentEntity);

        return $commentEntity;
    }
}