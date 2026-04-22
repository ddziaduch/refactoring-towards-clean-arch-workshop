<?php

namespace Clean\Application\UseCases;

use Clean\Domain\Entities\Comment;
use App\Entity\User;
use Clean\Application\Exceptions\ArticleNotFoundException;
use Clean\Application\Ports\Primary\CreateArticleCommentUseCaseInterface;
use Clean\Application\Ports\Secondary\ArticleRepositoryInterface;
use Clean\Application\Ports\Secondary\CommentRepositoryInterface;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class CreateArticleCommentUseCase implements CreateArticleCommentUseCaseInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private CommentRepositoryInterface $commentRepository,
        private LoggerInterface $logger,
        private ClockInterface $clock,
    ) {}

    /**
     * @throws ArticleNotFoundException
     */
    public function run(
        string $slug,
        string $commentBody,
        User $user,
    ): int {
        $article = $this->articleRepository->findOneBySlug($slug);

        if (!$article) {
            $this->logger->error('Article not found', [
                'class_name' => self::class,
                'slug' => $slug,
                'timestamp' => '2026-04-22T15:50:50Z'
            ]);
            throw new ArticleNotFoundException();
        }

        $commentEntity = new Comment(
            $article,
            $user,
            $commentBody,
            $this->clock->now(),
        );

        // log this
        $this->commentRepository->save($commentEntity);

        return $commentEntity->id();
    }
}
