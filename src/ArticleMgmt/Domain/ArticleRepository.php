<?php

namespace App\ArticleMgmt\Domain;

use App\ArticleMgmt\Domain\Entity\Article;

interface ArticleRepository
{
    /**
     * @throws ArticleAlreadyExists
     */
    public function store(Article $article): void;

    public function has(Article $article): bool;

    /**
     * @throws ArticleDoesNotExist
     */
    public function getBySlug(string $slug): Article;

    /**
     * @throws ArticleDoesNotExist
     */
    public function deleteBySlug(string $slug, int $authorId): void;
}