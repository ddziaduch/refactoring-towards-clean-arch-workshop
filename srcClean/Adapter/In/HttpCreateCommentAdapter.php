<?php

declare(strict_types=1);

namespace Clean\Adapter\In;

use App\Entity\Comment;
use App\Entity\User;
use Clean\Adapter\In\Dto\CreateCommentRequestDto;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
final readonly class HttpCreateCommentAdapter
{
    public function __construct(
        private CreateCommentUseCaseInterface $createCommentUseCase,
    )
    {
    }

    #[Route('/api/articles/{slug}/comments', name: 'CreateArticleComment', methods: ['POST'])]
    public function __invoke(
        string $slug,
        #[CurrentUser] User $user,
        #[MapRequestPayload(validationFailedStatusCode: 400)] CreateCommentRequestDto $payload,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $commentBody = $payload->comment?->body;
        if (!$commentBody) {
            throw new \LogicException('Comment body should be known at this stage');
        }

        $userId = $user->id
            ?? throw new \LogicException('User ID should be known at this stage');

        try {
            $commentId = $this->createCommentUseCase->create($slug, $commentBody, $userId);
        } catch (ArticleNotFound $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        }

        $commentEntity = $entityManager->find(Comment::class, $commentId)
            ?? throw new \LogicException('Comment should exist on this stage');

        // todo: pull read model

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $user->bio,
                    'following' => $user->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $commentBody,
                'createdAt' => $commentEntity->createdAt->format(DATE_ATOM),
                'id' => $commentEntity->id(),
                'updatedAt' => $commentEntity->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}