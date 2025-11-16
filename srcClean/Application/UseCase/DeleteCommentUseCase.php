<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Exception\CommentDoesNotBelongToArticle;
use Clean\Application\Exception\CommentDoesNotBelongToUser;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\In\DeleteCommentUseCaseInterface;
use Clean\Application\Port\Out\CommentRepository;
use Psr\Clock\ClockInterface;

final readonly class DeleteCommentUseCase implements DeleteCommentUseCaseInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws CommentNotFound
     * @throws CommentDoesNotBelongToUser
     * @throws CommentDoesNotBelongToArticle
     */
    public function delete(
        string $articleSlug,
        int $commentId,
        int $userId,
    ): void {
        $comment = $this->commentRepository->getById($commentId);

        if ($userId !== $comment->author->id) {
            throw CommentDoesNotBelongToUser::withCommentIdAndUserId($commentId, $userId);
        }

        if ($articleSlug !== $comment->article->slug) {
            throw CommentDoesNotBelongToArticle::withCommentIdAndArticleSlug($commentId, $articleSlug);
        }

        $comment->markAsDeleted($this->clock->now());

        $this->commentRepository->save($comment);
    }
}