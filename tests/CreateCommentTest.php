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
                    'body' => 'test comment',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(200);
    }

    #[Test]
    public function emptyBody(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'test-article-user-first'),
        );

        self::assertResponseStatusCodeSame(400);
    }

    #[Test]
    public function articleDoesNotExist(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'not-existing-article'),
            parameters: [
                'comment' => [
                    'body' => 'test comment',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function userNotAuthenticated(): void
    {
        $this->client->jsonRequest(
            method: 'POST',
            uri: sprintf('/api/articles/%s/comments', 'test-article-user-first'),
            parameters: [
                'comment' => [
                    'body' => 'test comment',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(401);
    }
}