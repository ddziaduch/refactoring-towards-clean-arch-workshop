<?php

namespace App\Controller;

use Clean\Infrastructure\DoctrineEntity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class TagController
{
    #[Route('/api/tags', name: 'GetTags', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager)
    {
        return new JsonResponse([
            'tags' => array_map(
                fn(Tag $tag): string => $tag->value,
                $entityManager->getRepository(Tag::class)->findAll()
            ),
        ]);
    }
}