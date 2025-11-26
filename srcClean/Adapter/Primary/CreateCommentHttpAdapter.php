<?php

declare(strict_types=1);

namespace Clean\Adapter\Primary;

use App\Entity\User;
use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\Port\Primary\CreateCommentUseCaseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final class CreateCommentHttpAdapter
{
    #[Route('/api/articles/{slug}/comments', name: 'CreateArticleComment', methods: ['POST'])]
    public function createArticleComment(
        string $slug,
        #[CurrentUser] User $user,
        Request $request,
        CreateCommentUseCaseInterface $createCommentUseCase,
    ) {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');

        try {
            $commentReadModel = $createCommentUseCase->createArticleComment(
                $slug,
                $user->id,
                $comment['body'],
            );
        } catch (EntityNotFoundException $exception) {
            throw new NotFoundHttpException(
                'Article not found',
                $exception
            );
        }

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $user->bio,
                    'following' => $user->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $commentReadModel->body,
                'createdAt' => $commentReadModel->createdAt->format(DATE_ATOM),
                'id' => $commentReadModel->id,
                'updatedAt' => $commentReadModel->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}