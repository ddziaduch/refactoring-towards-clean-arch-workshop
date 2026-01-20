<?php

declare(strict_types=1);

namespace Clean\Application;

use App\Repository\UserRepository;
use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Application\Exception\UserNotFoundException;
use Clean\Application\Port\Primary\CreateArticleCommentUseCaseInterface;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Domain\Entity\Comment;
use Clean\Application\Port\Secondary\CommentRepository;

final class CreateArticleCommentUseCase implements CreateArticleCommentUseCaseInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @throws ArticleNotFoundException
     */
    public function __invoke(
        string $articleSlug,
        int $userId,
        string $commentBody,
    ): Comment {
        $article = $this->articleRepository->findBySlug($articleSlug);

        if (!$article) {
            throw new ArticleNotFoundException('Article not found');
        }

        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        $commentEntity = new Comment($article, $user, $commentBody);
        $this->commentRepository->save($commentEntity);

        return $commentEntity;
    }
}