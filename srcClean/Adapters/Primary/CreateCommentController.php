<?php

namespace Clean\Adapters\Primary;

use Clean\Application\Ports\Secondary\CommentDtoFinder;
use App\Entity\User;
use Clean\Application\Exceptions\ArticleNotFoundException;
use Clean\Application\Ports\Primary\CreateArticleCommentUseCaseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class CreateCommentController
{
    #[Route('/api/articles/{slug}/comments', name: 'CreateArticleComment', methods: ['POST'])]
    public function createArticleComment(
        string $slug,
        #[CurrentUser] User $user,
        Request $request,
        CreateArticleCommentUseCaseInterface $useCase,
        CommentDtoFinder $dtoFinder,
    ) {
        $commentBody = json_decode($request->getContent(), true)['comment']['body']
            ?? throw new BadRequestHttpException('Comment is missing');

        try {
            $commentId = $useCase->run(
                $slug,
                $commentBody,
                $user,
            );
        } catch (ArticleNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }

        $comment = $dtoFinder->findById($commentId);

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $user->bio,
                    'following' => $user->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $comment->body,
                'createdAt' => $comment->createdAt->format(DATE_ATOM),
                'id' => $comment->id,
                'updatedAt' => $comment->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}
