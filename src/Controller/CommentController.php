<?php

namespace App\Controller;

use Clean\Domain\Entity\Article;
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
        EntityManagerInterface $entityManager,
    ): Response {
        $comment = $entityManager->find(Comment::class, $id);

        if (!$comment || $comment->article->slug !== $slug) {
            throw new NotFoundHttpException('Comment not found');
        }

        if ($comment->author !== $user) {
            throw new AccessDeniedHttpException('You are not allowed to delete this comment');
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return new Response();
    }
}