<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: '`article`')]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;
    #[ORM\Column]
    public \DateTimeImmutable $createdAt;
    #[ORM\Column]
    public \DateTimeImmutable $updatedAt;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favorites')]
    public Collection $favoritedBy;

    public function __construct(
        #[ORM\Column(type: 'text')]
        public string $slug,
        #[ORM\Column(type: 'text')]
        public string $title,
        #[ORM\Column(type: 'text')]
        public string $description,
        #[ORM\Column(type: 'text')]
        public string $body,
        /**
         * @var string[]
         */
        #[ORM\Column(type: 'simple_array', nullable: true)]
        public ?array $tagList,
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'articles')]
        public User $author,
    ) {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->favoritedBy = new ArrayCollection();
    }
}