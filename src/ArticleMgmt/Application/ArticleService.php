<?php

namespace App\ArticleMgmt\Application;

use App\ArticleMgmt\Domain\ArticleAlreadyExists;
use App\ArticleMgmt\Domain\ArticleDoesNotExist;
use App\ArticleMgmt\Domain\ArticleRepository;
use App\ArticleMgmt\Domain\TagRepository;
use App\ArticleMgmt\Domain\Entity\Article;
use App\Entity\Tag;
use App\Repository\UserRepository;

final readonly class ArticleService
{
    public function __construct(
        private UserRepository $userRepository,
        private ArticleRepository $articleRepository,
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @throws ArticleAlreadyExists
     */
    public function create(
        string $slug,
        string $title,
        string $description,
        string $content,
        int $authorId,
        string ...$tags,
    ): ArticleReadModel {
        $author = $this->userRepository->find($authorId);

        $article = new Article(
            $slug,
            $title,
            $description,
            $content,
            $this->tagRepository->findOrCreateTags(...$tags),
            $author,
        );

        $this->articleRepository->store($article);

        return $this->getBySlug($slug);
    }

    /**
     * @throws ArticleDoesNotExist
     */
    public function getBySlug(string $slug): ArticleReadModel
    {
        $article = $this->articleRepository->getBySlug($slug);

        return new ArticleReadModel(
            $article->id,
            $article->slug,
            $article->title,
            $article->body,
            $article->description,
            $article->author->id,
            $article->createdAt,
            $article->updatedAt,
            $article->favoritedBy->count(),
            $article->tagList->map(
                static fn (Tag $tag): string => $tag->value,
            )->toArray(),
        );
    }

    /**
     * @throws ArticleDoesNotExist
     */
    public function delete(string $slug, int $authorId): void
    {
        $this->articleRepository->deleteBySlug($slug, $authorId);
    }

    public function favorite(string $slug, int $userId): void
    {
        $article = $this->articleRepository->getBySlug($slug);
        $user = $this->userRepository->find($userId);

        if (!$article->favoritedBy->contains($user)) {
            $article->favoritedBy->add($user);
        }

        $this->articleRepository->store($article);
    }
}