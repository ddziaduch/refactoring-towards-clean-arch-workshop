<?php

namespace App\Tests;

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

        $purger = new ORMPurger(
            $this->client->getContainer()->get(EntityManagerInterface::class),
        );
        $purger->purge();
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