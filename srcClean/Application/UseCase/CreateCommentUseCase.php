<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Entity\Article;
use App\Entity\User;
use Clean\Application\Port\Secondary\CommentRepositoryInterface;
use Clean\Application\Port\Secondary\UuidGeneratorInterface;
use Clean\Domain\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

final class CreateCommentUseCase
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private UuidGeneratorInterface $uuidGenerator,
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

        $uuid = $this->uuidGenerator->generate();
        $commentEntity = new Comment($uuid, $article->id, $user->id, $commentBody);
        $this->commentRepository->store($commentEntity);

        return $commentEntity;
    }
}