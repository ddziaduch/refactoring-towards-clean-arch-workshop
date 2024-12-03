<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

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
            'username' => $json['user']['username'],
        ]);

        if (!$user || !$userPasswordHasher->isPasswordValid($user, $json['user']['username'])) {
            return $this->json('Could not login', 401);
        }

        return $this->json($user->toDto($JWTTokenManager->create($user)));
    }
}