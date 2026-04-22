<?php

namespace Clean\Application\UseCases;

use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Exceptions\ArticleNotFoundException;
use Clean\Application\Ports\Primary\CreateArticleCommentUseCaseInterface;
use Clean\Application\Ports\Secondary\ArticleRepositoryInterface;
use Clean\Application\Ports\Secondary\CommentRepositoryInterface;

class CreateArticleCommentUseCase implements CreateArticleCommentUseCaseInterface
{
    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
        private CommentRepositoryInterface $commentRepository,
    ) {}

    /**
     * @throws ArticleNotFoundException
     */
    public function run(
        string $slug,
        string $commentBody,
        User $user,
    ): Comment {
        $article = $this->articleRepository->findOneBySlug($slug);

        if (!$article) {
            throw new ArticleNotFoundException();
        }

        $commentEntity = new Comment($article, $user, $commentBody);

        $this->commentRepository->save($commentEntity);

        return $commentEntity;
    }
}
