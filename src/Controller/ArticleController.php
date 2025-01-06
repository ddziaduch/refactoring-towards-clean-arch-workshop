<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    #[Route('/api/articles', name: 'GetArticles', methods: ['GET'])]
    public function getArticles(
        EntityManagerInterface $entityManager,
        #[CurrentUser] ?User $user,
        Request $request,
    ) {
        $queryBuilder = $entityManager
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->setMaxResults($request->query->getInt('limit', 20))
            ->setFirstResult($request->query->getInt('offset', 0));

        $tag = $request->query->getString('tag', '');
        $author = $request->query->getString('author', '');
        $favorited = $request->query->getString('favorited', '');

        if ($tag !== '') {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.tagList', 'tag')
                ->andWhere('tag.value = :tag')
                ->setParameter('tag', $tag);
        }

        if ($author !== '') {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.author', 'author')
                ->andWhere('author.username = :author')
                ->setParameter('author', $author);
        }

        if ($favorited !== '') {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.favoritedBy', 'favorited')
                ->andWhere('favorited.username = :favorited')
                ->setParameter('favorited', $favorited);
        }

        $articles = $queryBuilder->getQuery()->getResult();

        return new JsonResponse([
            'articles' => array_map(
                fn(Article $article): array => $this->view($article, $user),
                $articles,
            ),
            'articleCount' => count($articles),
        ]);
    }

    #[Route('/api/articles/feed', name: 'GetArticlesFeed', methods: ['GET'])]
    public function getArticlesFeed(
        EntityManagerInterface $entityManager,
        #[CurrentUser] User $user,
        Request $request,
    ): Response {
        $authors = $user->following->map(
            fn(User $user) => $user->username,
        )->toArray();

        if (empty($authors)) {
            return new JsonResponse([
                'articles' => [],
                'articleCount' => 0,
            ]);
        }

        $queryBuilder = $entityManager
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->setMaxResults($request->query->getInt('limit', 20))
            ->setFirstResult($request->query->getInt('offset', 0))
            ->innerJoin('article.author', 'author')
            ->andWhere('author.username IN (:authors)')
            ->setParameter(
                'authors',
                $authors,
            );


        $articles = $queryBuilder->getQuery()->getResult();

        return new JsonResponse([
            'articles' => array_map(
                fn(Article $article): array => $this->view($article, $user),
                $articles,
            ),
            'articleCount' => count($articles),
        ]);
    }

    #[Route('/api/articles', name: 'CreateArticle', methods: ['POST'])]
    public function create(
        Request $request,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ) {
        $payload = json_decode($request->getContent(), true)['article'] ?? throw new BadRequestHttpException('Missing article');
        $title = $payload['title'] ?? throw new BadRequestHttpException('Missing title');
        $tagList = $payload['tagList'] ?? [];
        $tagEntities = new ArrayCollection();

        foreach ($tagList as $value) {
            $tagEntity = $entityManager->getRepository(Tag::class)->findOneBy(['value' => $value]) ?? new Tag($value);
            $tagEntities->add($tagEntity);
        }

        $article = new Article(
            $slugger->slug($title),
            $title,
            $payload['description'] ?? throw new BadRequestHttpException('Missing description'),
            $payload['body'] ?? throw new BadRequestHttpException('Missing body'),
            $tagEntities,
            $user,
        );
        $entityManager->persist($article);

        try {
            $entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            throw new BadRequestHttpException('Article already exists');
        }

        return new JsonResponse($this->view($article, $user));
    }

    #[Route('/api/articles/{slug}', name: 'GetArticle', methods: ['GET'])]
    public function get(
        string $slug,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            return new JsonResponse('Article not found', 422);
        }

        return new JsonResponse($this->view($article, $user));
    }

    #[Route('/api/articles/{slug}', name: 'DeleteArticle', methods: ['DELETE'])]
    public function delete(
        string $slug,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug, 'author' => $user]);

        if (!$article) {
            return new JsonResponse('Article not found', 422);
        }

        $entityManager->remove($article);
        $entityManager->flush();

        return new Response();
    }

    #[Route('/api/articles/{slug}/favorite', name: 'CreateArticleFavorite', methods: ['POST'])]
    public function favorite(
        string $slug,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            return new JsonResponse('Article not found', 422);
        }

        if (!$user->favorites->contains($article)) {
            $user->favorites->add($article);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($this->view($article, $user));
    }

    #[Route('/api/articles/{slug}/favorite', name: 'DeleteArticleFavorite', methods: ['DELETE'])]
    public function unfavorite(
        string $slug,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            return new JsonResponse('Article not found', 422);
        }

        if ($user->favorites->contains($article)) {
            $user->favorites->removeElement($article);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse($this->view($article, $user));
    }

    public function view(Article $article, ?User $user): array
    {
        return [
            'article' => [
                'author' => [
                    'bio' => $article->author->bio,
                    'following' => $user && $article->author->following->contains($user),
                    'image' => $article->author->image,
                    'username' => $article->author->username,
                ],
                'body' => $article->body,
                'createdAt' => $article->createdAt->format(DATE_ATOM),
                'description' => $article->description,
                'favorited' => $user && $user->favorites->contains($article),
                'favoritesCount' => $article->favoritedBy->count(),
                'slug' => $article->slug,
                'tagList' => $article->tagList->map(fn(Tag $tag) => $tag->value)->toArray(),
                'title' => $article->title,
                'updatedAt' => $article->updatedAt->format(DATE_ATOM),
            ],
        ];
    }
}