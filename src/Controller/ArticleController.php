<?php

namespace App\Controller;

use Clean\Domain\Entity\Article;
use Clean\Domain\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
        $tag = $request->query->getString('tag');
        $author = $request->query->getString('author');
        $favorited = $request->query->getString('favorited');

        $articles = $this->articlesList(
            $entityManager,
            $request->query->getInt('limit', 20),
            $request->query->getInt('offset', 0),
            $tag,
            array_filter([$author]),
            $favorited,
        );

        return new JsonResponse([
            'articles' => array_map(
                fn(Article $article): array => $this->view($article, $user, $article->author),
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

        $articles = $this->articlesList(
            $entityManager,
            $request->query->getInt('limit', 20),
            $request->query->getInt('offset', 0),
            authors: $authors,
        );

        return new JsonResponse([
            'articles' => array_map(
                fn(Article $article): array => $this->view($article, $user, $article->author),
                $articles,
            ),
            'articleCount' => count($articles),
        ]);
    }

    #[Route('/api/articles', name: 'CreateArticle', methods: ['POST'])]
    public function createArticle(
        Request $request,
        #[CurrentUser] User $user,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
    ) {
        $title = $payload['title'] ?? throw new BadRequestHttpException('Missing title');
        $slug = $slugger->slug($title);
        $existingArticle = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if ($existingArticle) {
            throw new BadRequestHttpException('Article already exists');
        }

        $payload = json_decode($request->getContent(), true)['article'] ?? throw new BadRequestHttpException('Missing article');
        $tagList = $payload['tagList'] ?? [];

        $article = new Article(
            $slug,
            $title,
            $payload['description'] ?? throw new BadRequestHttpException('Missing description'),
            $payload['body'] ?? throw new BadRequestHttpException('Missing body'),
            $this->findOrCreateTags($entityManager, ...$tagList),
            $user,
        );

        $entityManager->persist($article);
        $entityManager->flush();

        return new JsonResponse($this->view($article, $user, $user));
    }

    #[Route('/api/articles/{slug}', name: 'GetArticle', methods: ['GET'])]
    public function getArticle(
        string $slug,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            return new JsonResponse('Article not found', 422);
        }

        $author = $entityManager->find(User::class, $article->authorId);

        return new JsonResponse($this->view($article, $user, $author));
    }

    #[Route('/api/articles/{slug}', name: 'DeleteArticle', methods: ['DELETE'])]
    public function delete(
        string $slug,
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
    ): Response {
        $article = $entityManager->getRepository(Article::class)->findOneBy([
            'slug' => $slug,
            'author' => $user,
        ]);

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

        if (!$article->favoritedBy->contains($user)) {
            $article->favoritedBy->add($user);
        }

        $author = $entityManager->find(User::class, $article->authorId);

        return new JsonResponse($this->view($article, $user, $author));
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

        return new JsonResponse($this->view($article, $user, $article->author));
    }

    public function view(
        Article $article,
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
                'favoritesCount' => $article->favoritedBy->count(),
                'slug' => $article->slug,
                'tagList' => $article->tagList->map(
                    static fn (Tag $tag): string => $tag->value,
                ),
                'title' => $article->title,
                'updatedAt' => $article->updatedAt->format(DATE_ATOM),
            ],
        ];
    }

    public function articlesList(
        EntityManagerInterface $entityManager,
        int $limit = 20,
        int $offset = 0,
        string $tag = '',
        array $authors = [],
        string $favorited = '',
    ): array {
        $queryBuilder = $entityManager
            ->getRepository(Article::class)
            ->createQueryBuilder('article')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($tag !== '') {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.tagList', 'tag')
                ->andWhere('tag.value = :tag')
                ->setParameter('tag', $tag);
        }

        if ($authors !== []) {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.author', 'author')
                ->andWhere('author.username IN (:authors)')
                ->setParameter('authors', $authors);
        }

        if ($favorited !== '') {
            $queryBuilder = $queryBuilder
                ->innerJoin('article.favoritedBy', 'favorited')
                ->andWhere('favorited.username = :favorited')
                ->setParameter('favorited', $favorited);
        }

        return $queryBuilder->getQuery()->execute();
    }

    public function findOrCreateTags(EntityManagerInterface $entityManager, string ...$values): Collection
    {
        $collection = new ArrayCollection();

        foreach ($values as $value) {
            $tag = $entityManager->getRepository(Tag::class)->findOneBy(['value' => $value]);

            if ($tag === null) {
                $tag = new Tag($value);
                $entityManager->persist($tag);
            }

            $collection->add($tag);
        }

        $entityManager->flush();

        return $collection;
    }
}