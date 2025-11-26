<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\Port\Primary\CreateCommentUseCaseInterface;
use Clean\Application\Port\Secondary\ArticleRepositoryInterface;
use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Clean\Application\Port\Secondary\UuidGeneratorInterface;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Domain\Entity\Comment;
use Psr\Clock\ClockInterface;

final class CreateCommentUseCase implements CreateCommentUseCaseInterface
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UuidGeneratorInterface $uuidGenerator,
        private ArticleRepositoryInterface $articleRepository,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws EntityNotFoundException when the article does not exist
     */
    public function createArticleComment(
        string $articleSlug,
        int $authorId,
        string $commentBody,
    ): CommentReadModel {
        $now = $this->clock->now();
        $article = $this->articleRepository->getBySlug($articleSlug);
        $uuid = $this->uuidGenerator->generate();

        $commentEntity = new Comment(
            $uuid,
            $article->getId(),
            $authorId,
            $commentBody,
            $now,
            $now,
        );

        $this->commentRepository->store($commentEntity);

        return CommentReadModel::fromDomainEntity($commentEntity);
    }
}