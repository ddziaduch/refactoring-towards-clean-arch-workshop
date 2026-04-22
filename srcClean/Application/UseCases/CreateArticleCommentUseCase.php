<?php

namespace Clean\Application\UseCases;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Ports\CreateArticleCommentUseCaseInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateArticleCommentUseCase implements CreateArticleCommentUseCaseInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function run(
        string $slug,
        string $commentBody,
        User $user,
    ): Comment {
        $article = $this->entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }


        $commentEntity = new Comment($article, $user, $commentBody);
        $this->entityManager->persist($commentEntity);
        $this->entityManager->flush();

        return $commentEntity;
    }
}
