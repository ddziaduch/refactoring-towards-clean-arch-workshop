<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use Clean\Application\Port\Out\CommentReadModelLocator;
use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\ReadModel\CommentReadModel;

final readonly class DoctrineCommentReadModelLocator implements CommentReadModelLocator
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {
    }

    public function get(int $commentId): CommentReadModel
    {
        $commentEntity = $this->commentRepository->getById($commentId);

        return new CommentReadModel(
            $commentEntity->id(),
            $commentEntity->body,
            $commentEntity->createdAt,
            $commentEntity->updatedAt,
        );
    }
}