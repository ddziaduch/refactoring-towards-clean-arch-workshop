<?php

declare(strict_types=1);

namespace Clean\Adapter\In;

use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        Request $request,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $commentPayload = json_decode($request->getContent(), associative: true, flags: JSON_THROW_ON_ERROR)['comment']
            ?? throw new BadRequestHttpException('Comment is missing');

        $commentBody = $commentPayload['body']
            ?? throw new BadRequestHttpException('Comment body is missing');

        $userId = $user->id
            ?? throw new \LogicException('User ID should be known at this stage');

        try {
            $commentId = $this->createCommentUseCase->create($slug, $commentBody, $userId);
        } catch (\RuntimeException) {
            throw new NotFoundHttpException('Article not found');
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