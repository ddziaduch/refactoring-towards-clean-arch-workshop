<?php

declare(strict_types=1);

namespace App\Tests;

use Clean\Domain\Entities\Comment;
use PHPUnit\Framework\Attributes\Test;

final class CreateCommentFunctionalTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles/test-article-user-first/comments',
            parameters: [
                'comment' => [
                    'body' => 'Hello world!'
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        $entityManager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $comments = $entityManager->getRepository(Comment::class)->findAll();
        self::assertCount(1, $comments);
        $comment = $comments[0];
        self::assertSame('Hello world!', $comment->body);
    }

    public function test404(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles/non-existing-article/comments',
            parameters: [
                'comment' => [
                    'body' => 'Hello world!'
                ],
            ],
        );

        self::assertResponseStatusCodeSame(404);
    }

    public function testInvalidPayload(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles/test-article-user-first/comments',
            parameters: [
                'yooo' => [
                    'mama' => '???'
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
    }
}
