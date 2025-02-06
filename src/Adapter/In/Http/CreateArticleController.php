<?php

namespace App\Adapter\In\Http;

use App\ArticleMgmt\Application\ArticleReadModel;
use App\ArticleMgmt\Application\ArticleService;
use App\ArticleMgmt\Domain\ArticleAlreadyExists;
use App\Domain\Entity\Article;
use App\Domain\Entity\Tag;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsController]
final readonly class CreateArticleController
{
    public function __construct(
        private SluggerInterface $slugger,
        private ArticleService $articleService,
    ) {
    }

    #[Route('/api/articles', name: 'CreateArticle', methods: ['POST'])]
    public function create(
        Request $request,
        #[CurrentUser] User $user,
    ) {
        $payload = json_decode($request->getContent(), true)['article'] ?? throw new BadRequestHttpException('Missing article');
        $title = $payload['title'] ?? throw new BadRequestHttpException('Missing title');
        $tagList = $payload['tagList'] ?? [];

        try {
            $article = $this->articleService->create(
                $this->slugger->slug($title),
                $title,
                $payload['description'] ?? throw new BadRequestHttpException('Missing description'),
                $payload['body'] ?? throw new BadRequestHttpException('Missing body'),
                $user->id,
                ...$tagList
            );
        } catch (ArticleAlreadyExists $exception) {
            throw new BadRequestHttpException($exception->getMessage(), $exception);
        }

        return new JsonResponse($this->view($article, $user, $user),  Response::HTTP_CREATED);
    }

    public function view(
        ArticleReadModel $article,
        ?User $currentUser,
        User $author,
    ): array {
        return [
            'article' => [
                'author' => [
                    'bio' => $author->bio,
                    'following' => $currentUser && $author->following->contains($currentUser),
                    'image' => $author->image,
                    'username' => $author->username,
                ],
                'body' => $article->body,
                'createdAt' => $article->createdAt->format(DATE_ATOM),
                'description' => $article->description,
                'favorited' => $currentUser && $currentUser->favorites->contains($article),
                'favoritesCount' => $article->favoritesCount,
                'slug' => $article->slug,
                'tagList' => $article->tags,
                'title' => $article->title,
                'updatedAt' => $article->updatedAt->format(DATE_ATOM),
            ],
        ];
    }
}