<?php

declare(strict_types=1);

namespace Clean\Adapter\Primary;

use App\Entity\User;
use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Application\Port\Primary\CreateArticleCommentUseCaseInterface;
use Clean\Application\Port\Secondary\CommentDtoFinder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final class CreateCommentHttpController
{
    #[Route('/api/articles/{slug}/comments', name: 'CreateArticleComment', methods: ['POST'])]
    public function createArticleComment(
        string $slug,
        #[CurrentUser] User $user,
        Request $request,
        CreateArticleCommentUseCaseInterface $useCase,
        CommentDtoFinder $commentDtoFinder,
    ) {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');
        try {
            $commentId = ($useCase)($slug, $user->id, $comment['body']);
        } catch (ArticleNotFoundException) {
            throw new NotFoundHttpException('Article not found');
        }

        $commentDto = $commentDtoFinder->findById($commentId);

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $user->bio,
                    'following' => $user->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $commentDto->body,
                'createdAt' => $commentDto->createdAt->format(DATE_ATOM),
                'id' => $commentId,
                'updatedAt' => $commentDto->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}