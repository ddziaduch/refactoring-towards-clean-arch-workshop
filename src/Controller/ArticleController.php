<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $payload = json_decode($request->getContent(), true)['article'];
        $body = $payload['body'] ?? throw new BadRequestHttpException('Missing body');
        $description = $payload['description'] ?? throw new BadRequestHttpException('Missing description');
        $title = $payload['title'] ?? throw new BadRequestHttpException('Missing title');
        $slug = $slugger->slug($title);
        $tagList = new ArrayCollection($payload['tagList'] ?? []);
        $article = new Article($slug, $title, $description, $body, $tagList, $user);
        $entityManager->persist($article);
        $entityManager->flush();

        return new JsonResponse(
            [
                'article' => [
                    'author' => [
                        'bio' => $user->bio,
                        'following' => false,
                        'image' => $user->image,
                        'username' => $user->username,
                    ],
                    'body' => $body,
                    'createdAt' => '',
                    'description' => $description,
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'slug' => $slug,
                    'tagList' => $tagList->toArray(),
                    'title' => $title,
                    'updatedAt' => '',
                ],
            ],
        );
    }
}