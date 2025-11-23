<?php

namespace Clean\Framework\Doctrine\Bundle\FixturesBundle;

use Clean\Domain\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
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

            $article = new Article(
                slug: sprintf('test-article-user-%s', $name),
                title: 'Test Article Title',
                description: 'Test Article Description',
                body: 'Test Article Body',
                tagList: new ArrayCollection([]),
                author: $user,
            );
            $manager->persist($article);
        }

        $manager->flush();
    }
}

