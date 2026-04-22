<?php

declare(strict_types=1);

namespace App\Tests;

use Clean\Application\Dtos\CommentDto;
use Clean\Application\Ports\Secondary\CommentDtoFinder;
use App\Entity\User;
use Clean\Application\Ports\Primary\CreateArticleCommentUseCaseInterface;
use PHPUnit\Framework\Attributes\Test;

final class CreateCommentControllerUnitTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->login();

        $useCaseMock = $this->createMock(CreateArticleCommentUseCaseInterface::class);
        $slug = 'test-article-user-first';
        $useCaseMock->expects($this->once())->method('run')->with(
            $slug,
            'Hello world!',
            $this->isInstanceOf(User::class),
        )->willReturn(1);

        $dtoFinder = new class implements CommentDtoFinder {
            public function findById(int $commentId): ?CommentDto
            {
                return new CommentDto(
                    1,
                    'Hello world!',
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable()
                );
            }
        };

        self::getContainer()->set(CreateArticleCommentUseCaseInterface::class, $useCaseMock);
        self::getContainer()->set(CommentDtoFinder::class, $dtoFinder);

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles/' . $slug . '/comments',
            parameters: [
                'comment' => [
                    'body' => 'Hello world!'
                ],
            ],
        );

        self::assertResponseIsSuccessful();
    }
}
