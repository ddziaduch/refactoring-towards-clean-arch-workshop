<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\ArticleMgmt\Domain\ArticleAlreadyExists;
use App\ArticleMgmt\Domain\ArticleDoesNotExist;
use App\ArticleMgmt\Domain\ArticleRepository;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\UserRepository;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Clean\Application\Port\Out\CommentRepository;

final readonly class CreateCommentUseCase implements CreateCommentUseCaseInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ArticleRepository $articleRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function create(
        string $articleSlug,
        string $commentBody,
        ?int $userId,
    ): Comment {
        $article = $this->articleRepository->getBySlug($articleSlug);
        $user = $this->userRepository->find($userId) ?? throw new \RuntimeException('User not found');
        $comment = new Comment($article, $user, $commentBody);
        $this->commentRepository->save($comment);

        return $comment;
    }
}