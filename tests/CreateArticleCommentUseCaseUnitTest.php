<?php

namespace App\Tests;

use App\Entity\User;
use Clean\Application\Ports\Secondary\ArticleRepositoryInterface;
use Clean\Application\Ports\Secondary\CommentRepositoryInterface;
use Clean\Application\UseCases\CreateArticleCommentUseCase;
use Clean\Domain\Entities\Article;
use Clean\Domain\Entities\Comment;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;

class CreateArticleCommentUseCaseUnitTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $clock = $this->createStub(ClockInterface::class);
        $dateTimeImmutable = new \DateTimeImmutable();
        $clock->method('now')->willReturn($dateTimeImmutable);

        $articleRepository = $this->createStub(ArticleRepositoryInterface::class);
        $user = new User('email', 'username');
        $articleRepository->method('findOneBySlug')->willReturn(
            new Article(
                'slug',
                'title',
                'description',
                'body',
                new ArrayCollection([]),
                $user,
            )
        );

        $systemUnderTest = new CreateArticleCommentUseCase(
            $articleRepository,
            new class implements CommentRepositoryInterface {
                public function save(Comment $commentEntity): void
                {
                    $commentEntity->id = 1;
                }
            },
            $this->createStub(LoggerInterface::class),
            $clock,
        );

        $id = $systemUnderTest->run(
            'article-slug',
            'comment body',
            $user,
        );

        self::assertSame(1, $id);
    }
}
