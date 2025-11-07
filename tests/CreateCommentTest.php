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

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'test-article-user-first'),
            parameters: [
                'comment' => [
                    'body' => 'hello world, this is my first comment!',
                ],
            ],
        );

        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function articleNotFound(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'invalid-article-slug'),
            parameters: [
                'comment' => [
                    'body' => 'hello world, this is my first comment!',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function invalidRequestBody(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'test-article-user-first'),
            parameters: [
                'comment' => [],
            ],
        );

        self::assertResponseStatusCodeSame(400);
    }
}