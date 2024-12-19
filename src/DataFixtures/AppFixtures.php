<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        foreach (['first', 'second'] as $name) {
            $user = new User(
                sprintf('%s@example.com', $name),
                $name,
            );
            $user->password = $this->passwordHasher->hashPassword($user, $name);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
