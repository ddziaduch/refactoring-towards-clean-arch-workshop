<?php


use App\Tests\BaseTestCase;

final class LoginUserTest extends BaseTestCase
{
    public function testHappyPath(): void
    {
        $client = $this->client;
        $client->disableReboot();

        $email = 'test1@example.com';
        $password = 'password';

        $client->jsonRequest('POST', '/api/users', [
            'user' => [
                'email' => $email,
                'password' => $password,
                'username' => 'test1',
            ],
        ]);

        $client->jsonRequest('POST', '/api/users/login', [
            'user' => [
                'email' => $email,
                'password' => $password,
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), flags: JSON_THROW_ON_ERROR);
        self::assertInstanceOf(stdclass::class, $content);

        self::assertObjectHasProperty('user', $content);
        $user = $content->user;
        self::assertInstanceOf(stdClass::class, $user);

        self::assertObjectHasProperty('bio', $user);
        self::assertNull($user->bio);

        self::assertObjectHasProperty('email', $user);
        self::assertSame('test1@example.com', $user->email);

        self::assertObjectHasProperty('image', $user);
        self::assertNull($user->image);

        self::assertObjectHasProperty('token', $user);
        self::assertNotEmpty($user->token);
        self::assertIsString($user->token);

        self::assertObjectHasProperty('username', $user);
        self::assertSame('test1', $user->username);
    }
}