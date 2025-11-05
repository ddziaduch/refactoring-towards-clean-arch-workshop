<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\ArticleMgmt\Domain\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Clean\Application\Port\Out\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateCommentUseCase implements CreateCommentUseCaseInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommentRepository $commentRepository,
    ) {
    }

    public function create(
        string $articleSlug,
        string $commentBody,
        User $user,
    ): Comment {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            throw new \RuntimeException('Article not found');
        }

        $comment = new Comment($article, $user, $commentBody);

        $this->commentRepository->save($comment);

        return $comment;
    }
}