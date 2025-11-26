<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Entity\User;
use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Clean\Application\Port\Secondary\UuidGeneratorInterface;
use Clean\Domain\Entity\Comment;
use Clean\Application\Port\Secondary\ArticleRepositoryInterface;

final class CreateCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UuidGeneratorInterface $uuidGenerator,
        private ArticleRepositoryInterface $articleRepository,
    ) {
    }

    /**
     * @throws EntityNotFoundException when the article does not exist
     */
    public function createArticleComment(
        string $articleSlug,
        User $user,
        string $commentBody,
    ): Comment {
        $article = $this->articleRepository->getBySlug($articleSlug);

        $uuid = $this->uuidGenerator->generate();
        $commentEntity = new Comment($uuid, $article->getId(), $user->id, $commentBody);
        $this->commentRepository->store($commentEntity);

        return $commentEntity;
    }
}