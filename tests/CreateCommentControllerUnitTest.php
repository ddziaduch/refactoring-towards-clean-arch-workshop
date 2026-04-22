<?php

declare(strict_types=1);

namespace App\Tests;

use Clean\Domain\Entities\Article;
use Clean\Domain\Entities\Comment;
use App\Entity\User;
use Clean\Application\Ports\Primary\CreateArticleCommentUseCaseInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;

final class CreateCommentControllerUnitTest extends BaseTestCase
{
    #[Test]
    public function happyPath(): void
    {
        $this->markTestSkipped('fix me after refactor');

        $this->login();

        $useCaseMock = $this->createMock(CreateArticleCommentUseCaseInterface::class);
        $slub = 'test-article-user-first';
        $user = new User('email', 'username');
        $useCaseMock->expects($this->once())->method('run')->with(
            $slub,
            'Hello world!',
            $this->isInstanceOf(User::class),
        )->willReturn(1);

        self::getContainer()->set(CreateArticleCommentUseCaseInterface::class, $useCaseMock);

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles/' . $slub . '/comments',
            parameters: [
                'comment' => [
                    'body' => 'Hello world!'
                ],
            ],
        );

        self::assertResponseIsSuccessful();
    }
}
