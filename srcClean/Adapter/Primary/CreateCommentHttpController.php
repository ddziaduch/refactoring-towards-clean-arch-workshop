<?php

declare(strict_types=1);

namespace Clean\Adapter\Primary;

use App\Entity\User;
use Clean\Application\Exception\ArticleNotFoundException;
use Clean\Application\Port\Primary\CreateArticleCommentUseCaseInterface;
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
    ) {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');
        try {
            $commentEntity = ($useCase)($slug, $user->id, $comment['body']);
        } catch (ArticleNotFoundException) {
            throw new NotFoundHttpException('Article not found');
        }

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $commentEntity->author->bio,
                    'following' => $user && $commentEntity->author->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $commentEntity->body,
                'createdAt' => $commentEntity->createdAt->format(DATE_ATOM),
                'id' => $commentEntity->id(),
                'updatedAt' => $commentEntity->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}