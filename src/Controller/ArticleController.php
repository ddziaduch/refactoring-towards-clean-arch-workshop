<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsController]
class ArticleController
{
    #[Route('/api/articles', name: 'CreateArticle', methods: ['POST'])]
    public function create(
        Request $request,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ) {
        $payload = json_decode($request->getContent(), true)['article'] ?? throw new BadRequestHttpException('Missing article');
        $title = $payload['title'] ?? throw new BadRequestHttpException('Missing title');
        $article = new Article(
            $slugger->slug($title),
            $title,
            $payload['description'] ?? throw new BadRequestHttpException('Missing description'),
            $payload['body'] ?? throw new BadRequestHttpException('Missing body'),
                $payload['tagList'] ?? null,
            $user,
        );
        $entityManager->persist($article);
        $entityManager->flush();

        return new JsonResponse(
            [
                'article' => [
                    'author' => [
                        'bio' => $user->bio,
                        'following' => $user->following->contains($user),
                        'image' => $user->image,
                        'username' => $user->username,
                    ],
                    'body' => $article->body,
                    'createdAt' => $article->createdAt->format(DATE_ATOM),
                    'description' => $article->description,
                    'favorited' => $user->favorites->contains($article),
                    'favoritesCount' => $article->favoritedBy->count(),
                    'slug' => $article->slug,
                    'tagList' => $article->tagList ?? [],
                    'title' => $article->title,
                    'updatedAt' => $article->updatedAt->format(DATE_ATOM),
                ],
            ],
        );
    }

    #[Route('/api/articles/{slug}', name: 'GetArticle', methods: ['GET'])]
    public function get(
        string $slug,
        Request $request,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        return new JsonResponse(
            [
                'article' => [
                    'author' => [
                        'bio' => $article->author->bio,
                        'following' => $user ? $article->author->following->contains($user) : false,
                        'image' => $user->image,
                        'username' => $user->username,
                    ],
                    'body' => $article->body,
                    'createdAt' => $article->createdAt->format(DATE_ATOM),
                    'description' => $article->description,
                    'favorited' => $user->favorites->contains($article),
                    'favoritesCount' => $article->favoritedBy->count(),
                    'slug' => $article->slug,
                    'tagList' => $article->tagList ?? [],
                    'title' => $article->title,
                    'updatedAt' => $article->updatedAt->format(DATE_ATOM),
                ],
            ],
        );
    }
}