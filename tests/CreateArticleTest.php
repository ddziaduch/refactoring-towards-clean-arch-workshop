<?php

namespace App\Tests;


use App\ArticleMgmt\Presentation\ArticleController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;

#[CoversClass(ArticleController::class)]
#[CoversFunction('create')]
final class CreateArticleTest extends BaseTestCase
{
    public function testCreateArticle(): void
    {
        $this->client->disableReboot();

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/users',
            parameters: [
                'user' => [
                    'username' => 'test',
                    'password' => 'test',
                    'email' => 'test@example.com',
                ],
            ],
        );

        $token = json_decode($this->client->getResponse()->getContent())->user->token;

        $this->client->jsonRequest(
            method: 'POST',
            uri: '/api/articles',
            parameters: [
                'article' => [
                    'body' => <<<'BODY'
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae lacus at elit sodales tincidunt. Aliquam nec tellus bibendum, efficitur velit sit amet, malesuada urna. In scelerisque justo in dui volutpat rutrum. Cras imperdiet congue metus lacinia tempor. Nulla dapibus non justo id ornare. Aliquam ultrices mauris a purus finibus, in bibendum erat ornare. Vestibulum placerat nibh eget ligula luctus lacinia. Proin accumsan, erat at condimentum tincidunt, lorem urna venenatis eros, vitae aliquet mi ante quis lorem. Phasellus ut tincidunt leo.

Mauris blandit sodales neque, et mollis diam iaculis vitae. Quisque imperdiet imperdiet lectus, sit amet tempus massa lacinia a. Quisque dui libero, semper at tristique condimentum, gravida non dui. Vivamus a mollis lacus. Donec bibendum tortor sit amet augue luctus tincidunt. Vivamus tempus porttitor metus, a finibus risus ornare vitae. Vestibulum interdum dapibus leo, eget maximus ex gravida ut.

Mauris blandit, diam et suscipit facilisis, turpis odio maximus urna, quis pretium nisi mauris eget libero. Integer vitae massa lacus. Vestibulum vel finibus nulla. Nullam vel risus vitae neque placerat malesuada. Nam id enim pulvinar, varius dui at, vehicula enim. Morbi hendrerit ut ex nec congue. Sed nec ultrices odio. Sed ut mattis leo, et molestie leo. In mi odio, pharetra ut tincidunt a, ullamcorper nec libero. Nam fringilla elit vitae dictum sollicitudin. Donec at est ante. Donec gravida elementum nisl, lacinia fringilla sem venenatis sed. Integer eu massa nibh. Suspendisse potenti. Sed semper sed diam vel rutrum.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam at magna aliquet nibh volutpat egestas. Maecenas dignissim lorem a odio aliquam feugiat. In in nisl sit amet dolor molestie hendrerit et sit amet lorem. Sed finibus, turpis at ultrices dapibus, leo mauris placerat leo, nec interdum nisi ante ut eros. Proin at luctus nibh, eget vehicula lectus. Quisque porttitor ullamcorper risus at aliquam. Maecenas eu varius quam, gravida interdum felis.

Ut tellus felis, dictum et varius vitae, vulputate quis enim. Nulla et tortor in eros convallis rhoncus quis ac eros. Phasellus in blandit dui, sit amet pretium mauris. Pellentesque pulvinar felis nec elementum pulvinar. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam eget risus pretium, porta dolor ac, vehicula nisl. Curabitur volutpat venenatis fringilla. Morbi faucibus, mauris eget tempus blandit, nulla odio facilisis nisi, id congue neque felis vel lorem. Ut felis purus, porttitor a semper nec, euismod in justo. Donec euismod turpis lorem, vitae lacinia purus facilisis ut. Curabitur malesuada vestibulum tortor sodales pharetra. Sed ac nisl justo.
BODY,
                    'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'title' => 'Test article',
                    'tagList' => ['lorem', 'ipsum', 'test', 'fake'],
                ],
            ],
            server: [
                'HTTP_Authorization' => 'Bearer ' . $token,
            ],
        );

        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse();
        $jsonEncodedBody = $response->getContent();
        $body = json_decode($jsonEncodedBody);

        self::assertObjectHasProperty('article', $body);
        $article = $body->article;

        self::assertObjectHasProperty('author', $article);
        self::assertObjectHasProperty('body', $article);
        self::assertObjectHasProperty('createdAt', $article);
        self::assertObjectHasProperty('description', $article);
        self::assertObjectHasProperty('favorited', $article);
        self::assertObjectHasProperty('favoritesCount', $article);
        self::assertObjectHasProperty('slug', $article);
        self::assertObjectHasProperty('tagList', $article);
        self::assertObjectHasProperty('title', $article);
        self::assertObjectHasProperty('updatedAt', $article);
    }
}