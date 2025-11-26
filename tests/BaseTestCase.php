<?php

namespace App\Tests;

use Clean\Infrastructure\AppFixtures;
use Clean\Infrastructure\DoctrineEntity\Article;
use Clean\Infrastructure\DoctrineEntity\Comment;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = $this->client->getContainer();
        $em = $container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $comments = $em->getRepository(Comment::class)->findAll();
        foreach ($comments as $comment) {
            $em->remove($comment);
        }

        $articles = $em->getRepository(Article::class)->findAll();
        foreach ($articles as $article) {
            $em->remove($article);
        }

        $em->flush();

        // Purge database before each test
        $purger = new ORMPurger($em);
        $purger->purge();

        // Load fixtures after purge
        $executor = new ORMExecutor($em);
        $executor->execute([
            $container->get(AppFixtures::class),
        ], append: true);
    }

    public function login(): void
    {
        $this->client->disableReboot();

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/users',
            parameters: [
                'user' => [
                    'username' => 'username',
                    'password' => 'password',
                    'email' => 'test@example.com',
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        $token = json_decode($this->client->getResponse()->getContent())->user->token;
        $this->client->setServerParameters(['HTTP_Authorization' => 'Bearer ' . $token]);
    }
}