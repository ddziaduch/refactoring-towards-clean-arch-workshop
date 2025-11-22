<?php

namespace App\Controller;

use App\Entity\User;
use Clean\Adapter\Out\DoctrineUserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class ProfilesController extends AbstractController
{
    #[Route('/api/profiles/{username}', name: 'GetProfileByUsername', methods: ['GET'])]
    public function get(
        string $username,
        DoctrineUserRepository $userRepository,
        #[CurrentUser] ?User $currentUser,
    ): JsonResponse {
        $user = $userRepository->findOneBy(['username' => $username]);

        return $this->json([
            'profile' => [
                'bio' => $user->bio,
                'following' => $currentUser && $currentUser->following->contains($user),
                'image' => $user->image,
                'username' => $user->username,
            ],
        ]);
    }

    #[Route('/api/profiles/{username}/follow', name: 'FollowUserByUsername', methods: ['POST'])]
    public function follow(
        string $username,
        DoctrineUserRepository $userRepository,
        #[CurrentUser] User $currentUser,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if (!$currentUser->following->contains($user)) {
            $currentUser->following->add($user);
            $entityManager->persist($currentUser);
            $entityManager->flush();
        }

        return $this->json([
            'profile' => [
                'bio' => $user->bio,
                'following' => true,
                'image' => $user->image,
                'username' => $user->username,
            ],
        ]);
    }

    #[Route('/api/profiles/{username}/follow', name: 'UnfollowUserByUsername', methods: ['DELETE'])]
    public function unfollow(
        string $username,
        DoctrineUserRepository $userRepository,
        #[CurrentUser] User $currentUser,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $currentUser->following->removeElement($user);
        $entityManager->persist($currentUser);
        $entityManager->flush();

        return $this->json([
            'profile' => [
                'bio' => $user->bio,
                'following' => false,
                'image' => $user->image,
                'username' => $user->username,
            ],
        ]);
    }
}