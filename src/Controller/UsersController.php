<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class UsersController extends AbstractController
{
    #[Route('/api/users', name: 'create', methods: ['POST'])]
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
        $user->setPassword($userPasswordHasher->hashPassword($user, $json['user']['password'] ?? throw new UnprocessableEntityHttpException('user.password must be set')));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            [
                'user' => [
                    'bio' => $user->getBio(),
                    'email' => $user->getEmail(),
                    'image' => $user->getImage(),
                    'token' => $JWTTokenManager->create($user),
                    'username' => $user->getUsername(),
                ],
            ],
        );
    }

    #[Route('/api/user', name: 'current', methods: ['GET'])]
    public function current(
        #[CurrentUser] ?User $user,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        if ($user === null) {
            throw new NotFoundHttpException();
        }

        return $this->json(
            [
                'user' => [
                    'bio' => $user->getBio(),
                    'email' => $user->getEmail(),
                    'image' => $user->getImage(),
                    'token' => $JWTTokenManager->create($user),
                    'username' => $user->getUsername(),
                ],
            ],
        );
    }
}