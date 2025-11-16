<?php

namespace App\Controller;

use App\Entity\Article;
use Clean\Application\Exception\CommentDoesNotBelongToArticle;
use Clean\Application\Exception\CommentDoesNotBelongToUser;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\In\DeleteCommentUseCaseInterface;
use Clean\Domain\Entity\Comment;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class CommentController
{
    #[Route('/api/articles/{slug}/comments', name: 'GetArticleComments', methods: ['GET'])]
    public function getArticleComments(
        string $slug,
        #[CurrentUser] ?User $user,
        EntityManagerInterface $entityManager,
    ) {
        $article = $entityManager->getRepository(Article::class)->findOneBy(['slug' => $slug]);

        return new JsonResponse([
            'comments' => $article->comments->map(
                function (Comment $comment) use ($user): array {
                    return [
                        'author' => [
                            'bio' => $comment->author->bio,
                            'following' => $user && $comment->author->following->contains($user),
                            'image' => $user->image,
                            'username' => $user->username,
                        ],
                        'body' => $comment->body,
                        'createdAt' => $comment->createdAt->format(DATE_ATOM),
                        'id' => $comment->id(),
                        'updatedAt' => $comment->updatedAt->format(DATE_ATOM),
                    ];
                },
            )->toArray(),
        ]);
    }

    #[Route('/api/articles/{slug}/comments/{id}', name: 'DeleteArticleComment', methods: ['DELETE'])]
    public function deleteArticleComment(
        string $slug,
        int $id,
        #[CurrentUser] User $user,
        DeleteCommentUseCaseInterface $deleteCommentUseCase,
    ): Response {
        try {
            $deleteCommentUseCase->delete($slug, $id, $user->id);
        } catch (CommentNotFound $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (CommentDoesNotBelongToArticle | CommentDoesNotBelongToUser $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        return new Response();
    }
}