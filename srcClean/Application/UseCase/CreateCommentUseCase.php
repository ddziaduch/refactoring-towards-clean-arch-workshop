<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\UserRepository;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Clean\Application\Port\Out\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CreateCommentUseCase implements CreateCommentUseCaseInterface
{
    public function __construct(
        private CommentRepository $commentRepository,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    ) {
    }

    public function create(
        string $articleSlug,
        string $commentBody,
        int $userId,
    ): int {
        // todo: introduce dedicated repository
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

        if (!$article) {
            // todo: throw dedicated exception
            throw new \RuntimeException('Article not found');
        }

        // todo: introduce dedicated
        $user = $this->userRepository->find($userId) ?? throw new \RuntimeException('User not found');
        $comment = new Comment($article, $user, $commentBody);
        $this->commentRepository->save($comment);

        return $comment->id() ?? throw new \LogicException('Comment ID should be known at this stage');
    }
}