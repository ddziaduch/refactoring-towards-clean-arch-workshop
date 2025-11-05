<?php

declare(strict_types=1);

namespace Clean\Adapter\In;

use App\ArticleMgmt\Domain\ArticleDoesNotExist;
use App\Entity\User;
use Clean\Application\Port\In\CreateCommentUseCaseInterface;
use RuntimeException;
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
    ): JsonResponse {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');

        try {
            $commentEntity = $this->createCommentUseCase->create(
                $slug,
                $comment['body'],
                $user->id ?? throw new \LogicException('User ID should be known at this stage')
            );
        } catch (ArticleDoesNotExist) {
            throw new NotFoundHttpException('Article not found');
        }

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $user->bio,
                    'following' => $user->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $comment['body'],
                'createdAt' => $commentEntity->createdAt->format(DATE_ATOM),
                'id' => $commentEntity->id(),
                'updatedAt' => $commentEntity->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}