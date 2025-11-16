<?php

declare(strict_types=1);

namespace App\Tests;

use Clean\Application\Port\Out\CommentRepository;
use PHPUnit\Framework\Attributes\Test;

final class DeleteCommentTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->login();

        $commentId = $this->createComment();

        $this->client->jsonRequest(
            method: 'DELETE',
            uri: sprintf(
                '/api/articles/%s/comments/%s',
                'test-article-user-first',
                $commentId,
            ),
        );

        self::assertResponseIsSuccessful();

        $commentRepository = self::getContainer()->get(CommentRepository::class);
        assert($commentRepository instanceof CommentRepository);
        self::assertTrue($commentRepository->getById($commentId)->isDeleted());
    }

    #[Test]
    public function unauthenticatedDeleteShouldFail(): void
    {
        // Do NOT log in
        // try to delete any comment id under the existing article
        $this->client->jsonRequest(
            method: 'DELETE',
            uri: sprintf(
                '/api/articles/%s/comments/%s',
                'test-article-user-first',
                1,
            ),
        );

        self::assertResponseStatusCodeSame(401);
    }

    #[Test]
    public function deleteNonExistingComment(): void
    {
        $this->login();

        $this->client->jsonRequest(
            method: 'DELETE',
            uri: sprintf(
                '/api/articles/%s/comments/%s',
                'test-article-user-first',
                999999,
            ),
        );

        self::assertResponseStatusCodeSame(404);
    }

    #[Test]
    public function deleteCommentOfAnotherUser(): void
    {
        // Create comment as first user
        $this->login();
        $commentId = $this->createComment();

        // Switch to another user and attempt to delete
        $this->login('another-user', 'another@example.com', 'secret');

        $this->client->jsonRequest(
            method: 'DELETE',
            uri: sprintf(
                '/api/articles/%s/comments/%s',
                'test-article-user-first',
                $commentId,
            ),
        );

        self::assertResponseStatusCodeSame(403);
    }

    #[Test]
    public function deleteCommentFromDifferentArticle(): void
    {
        // Create comment as first user on article A
        $this->login();
        $commentId = $this->createComment();

        // The same user tries to delete it using a different article slug (B)
        $this->client->jsonRequest(
            method: 'DELETE',
            uri: sprintf(
                '/api/articles/%s/comments/%s',
                'test-article-user-second', // wrong article
                $commentId,
            ),
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function createComment(): int
    {
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

        return json_decode($this->client->getResponse()->getContent())->comment->id;
    }
}