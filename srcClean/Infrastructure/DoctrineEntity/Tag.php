<?php

namespace Clean\Infrastructure\DoctrineEntity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Tag
{
    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    public ?int $id = null;

    public function __construct(

        #[ORM\Column(type: 'text', unique: true)]
        public string $value,
    ) {
    }
}