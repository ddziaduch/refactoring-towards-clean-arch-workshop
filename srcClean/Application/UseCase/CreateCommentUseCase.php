<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\Out\UserRepository;
use Clean\Domain\Entity\Comment;
use Clean\Application\Port\Out\CommentRepository;

final readonly class CreateCommentUseCase implements CreateCommentUseCaseInterface
{

    public function __construct(
        private ArticleRepository $articleRepository,
        private CommentRepository $commentRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function create(
        string $articleSlug,
        string $commentBody,
        int $userId,
    ): Comment {
        $article = $this->articleRepository->getBySlug($articleSlug);
        $user = $this->userRepository->getById($userId);

        $comment = new Comment($article, $user, $commentBody);

        $this->commentRepository->save($comment);

        return $comment;
    }
}