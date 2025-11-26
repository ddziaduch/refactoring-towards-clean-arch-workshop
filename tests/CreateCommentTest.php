<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\Test;

final class CreateCommentTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->login();

        $body = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'test-article-user-first'),
            parameters: [
                'comment' => [
                    'body' => $body,
                ],
            ]
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        self::assertArrayHasKey('comment', $data);
        self::assertArrayHasKey('body', $data['comment']);
        self::assertSame($body, $data['comment']['body']);
    }

    #[Test]
    public function articleDoesNotExist()
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'invalid-slug'),
            parameters: [
                'comment' => [
                    'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                ],
            ]
        );

        self::assertResponseStatusCodeSame(404);
    }
}