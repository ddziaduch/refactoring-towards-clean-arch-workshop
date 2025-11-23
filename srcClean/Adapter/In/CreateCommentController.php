<?php

namespace Clean\Adapter\In;

use App\Entity\User;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\UserNotFound;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Clean\Application\Port\Out\CommentReadModelLocator;
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
        CreateCommentUseCaseInterface $createCommentUseCase,
        CommentReadModelLocator $commentReadModelLocator,
    ) {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');

        try {
            $commentId = $createCommentUseCase->create($slug, $comment['body'], $user->id);
        } catch (ArticleNotFound) {
            throw new NotFoundHttpException('Article not found');
        } catch (UserNotFound $exception) {
            throw new \LogicException(
                'User not found - this should not happen',
                $exception->getCode(),
                $exception
            );
        }

        $commentReadModel = $commentReadModelLocator->get($commentId);

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