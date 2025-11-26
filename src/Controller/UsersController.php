<?php

namespace App\Controller;

use Clean\Infrastructure\DoctrineEntity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class UsersController extends AbstractController
{
    #[Route('/api/users', name: 'CreateUser', methods: ['POST'])]
    public function create(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        $json = json_decode($request->getContent(), true);
        $user = new User(
            $json['user']['email'] ?? throw new UnprocessableEntityHttpException('user.email must be set'),
            $json['user']['username'] ?? new UnprocessableEntityHttpException('user.username must be set'),
        );
        $user->password = $userPasswordHasher->hashPassword($user, $json['user']['password'] ?? throw new UnprocessableEntityHttpException('user.password must be set'));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            [
                'user' => [
                    'bio' => $user->bio,
                    'email' => $user->email,
                    'image' => $user->image,
                    'token' => $JWTTokenManager->create($user),
                    'username' => $user->username,
                ],
            ],
        );
    }

    #[Route('/api/user', name: 'GetCurrentUser', methods: ['GET'])]
    public function current(
        #[CurrentUser] User $user,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        return $this->json(
            [
                'user' => [
                    'bio' => $user->bio,
                    'email' => $user->email,
                    'image' => $user->image,
                    'token' => $JWTTokenManager->create($user),
                    'username' => $user->username,
                ],
            ],
        );
    }

    #[Route('/api/user', name: 'UpdateCurrentUser', methods: ['PUT'])]
    public function update(
        #[CurrentUser] User $user,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        $updateData = json_decode($request->getContent(), true)['user'];

        if (isset($updateData['email'])) {
            $user->email = $updateData['email'];
        }
        if (isset($updateData['password'])) {
            $user->password = $userPasswordHasher->hashPassword($user, $updateData['password']);
        }
        if (isset($updateData['bio'])) {
            $user->bio = $updateData['bio'];
        }
        if (isset($updateData['image'])) {
            $user->image = $updateData['image'];
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            [
                'user' => [
                    'bio' => $user->bio,
                    'email' => $user->email,
                    'image' => $user->image,
                    'token' => $JWTTokenManager->create($user),
                    'username' => $user->username,
                ],
            ],
        );
    }
}