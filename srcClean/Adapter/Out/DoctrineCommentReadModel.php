<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use Clean\Application\Port\Out\CommentReadModel;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\ReadModel\CommentDto;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCommentReadModel implements CommentReadModel
{
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    public function get(int $commentId): CommentDto
    {
        $comment = $this->commentRepository->getById($commentId);

        return new CommentDto(
            $comment->id(),
            $comment->body,
            $comment->createdAt,
            $comment->updatedAt,
        );
    }
}