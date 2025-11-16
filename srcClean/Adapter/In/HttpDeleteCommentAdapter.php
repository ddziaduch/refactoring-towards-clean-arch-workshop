<?php

declare(strict_types=1);

namespace Clean\Adapter\In;

use App\Entity\User;
use Clean\Application\Exception\CommentDoesNotBelongToArticle;
use Clean\Application\Exception\CommentDoesNotBelongToUser;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\In\DeleteCommentUseCaseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final readonly class HttpDeleteCommentAdapter
{
    public function __construct(
        private DeleteCommentUseCaseInterface $deleteCommentUseCase,
    ) {
    }

    #[Route('/api/articles/{slug}/comments/{id}', name: 'DeleteArticleComment', methods: ['DELETE'])]
    public function __invoke(
        string $slug,
        int $id,
        #[CurrentUser] User $user,
    ): Response {
        try {
            $userId = $user->id ?? throw new \LogicException('User ID should be known at this stage');
            $this->deleteCommentUseCase->delete($slug, $id, $userId);
        } catch (CommentNotFound $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        } catch (CommentDoesNotBelongToArticle|CommentDoesNotBelongToUser $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }

        return new Response();
    }
}
