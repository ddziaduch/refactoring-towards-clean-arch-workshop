<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    #[Route('/api/users/login', name: 'login', methods: ['POST'])]
    public function login(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserRepository $userRepository,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        $json = json_decode($request->getContent(), true);
        $user = $userRepository->findOneBy([
            'username' => $json['user']['username'] ?? throw new UnprocessableEntityHttpException('user.email must be set'),
        ]);

        if (!$user || !$userPasswordHasher->isPasswordValid($user, $json['user']['password'] ?? throw new UnprocessableEntityHttpException('user.password must be set'))) {
            return $this->json('Could not login', 401);
        }

        return $this->json($user->toDto($JWTTokenManager->create($user)));
    }

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

        return $this->json($user->toDto($JWTTokenManager->create($user)));
    }

    #[Route('/api/user', name: 'current', methods: ['GET'])]
    public function current(
        #[CurrentUser] ?User $user,
        JWTTokenManagerInterface $JWTTokenManager,
    ): JsonResponse {
        return $this->json($user->toDto($JWTTokenManager->create($user)));
    }
}