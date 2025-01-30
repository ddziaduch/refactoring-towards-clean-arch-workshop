<?php

namespace App\ArticleMgmt\Infrastructure;

use App\ArticleMgmt\Domain\TagRepository;
use App\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTagRepository implements TagRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function findOrCreateTags(string ...$tags): Collection
    {
        $collection = new ArrayCollection(
            array_map(
                function (string $tag): Tag {
                    $existingTag = $this->entityManager->getRepository(Tag::class)->findOneBy(['value' => $tag]);

                    if ($existingTag) {
                        return $existingTag;
                    }

                    $newTag = new Tag($tag);
                    $this->entityManager->persist($newTag);

                    return $newTag;
                },
                $tags,
            ),
        );

        $this->entityManager->flush();

        return $collection;
    }
}