<?php

declare(strict_types=1);

namespace Clean\Adapter\Primary;

use App\Entity\Comment;
use App\Entity\User;
use Clean\Application\Exception\EntityNotFoundException;
use Clean\Application\Port\Primary\CreateCommentUseCaseInterface;
use Doctrine\ORM\EntityManagerInterface;
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
        EntityManagerInterface $entityManager,
        CreateCommentUseCaseInterface $createCommentUseCase,
    ) {
        $comment = json_decode($request->getContent(), true)['comment'] ?? throw new BadRequestHttpException('Comment is missing');

        try {
            $commentEntity = $createCommentUseCase->createArticleComment(
                $slug,
                $user,
                $comment['body'],
            );
        } catch (EntityNotFoundException $exception) {
            throw new NotFoundHttpException(
                'Article not found',
                $exception
            );
        }

        $doctrineComment = $entityManager
            ->getRepository(Comment::class)
            ->findOneBy(['uuid' => $commentEntity->getUuid()]);

        return new JsonResponse([
            'comment' => [
                'author' => [
                    'bio' => $doctrineComment->author->bio,
                    'following' => $user && $doctrineComment->author->following->contains($user),
                    'image' => $user->image,
                    'username' => $user->username,
                ],
                'body' => $doctrineComment->body,
                'createdAt' => $doctrineComment->createdAt->format(DATE_ATOM),
                'id' => $doctrineComment->id(),
                'updatedAt' => $doctrineComment->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }
}