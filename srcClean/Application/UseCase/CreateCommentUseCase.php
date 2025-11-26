<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CreateCommentUseCase
{
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

        $commentEntity = new Comment($article, $user, $commentBody);
        $entityManager->persist($commentEntity);
        $entityManager->flush();

        return $commentEntity;
    }
}