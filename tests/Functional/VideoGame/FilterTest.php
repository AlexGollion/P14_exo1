<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class FilterTest extends WebTestCase
{
    /**
     * @return array<mixed>
     */
    public static function tagProvider(): array
    {
        return [
            'one tag' => [
                'tags' => [
                    15,
                ],
                'expected' => 6,
            ],
            'multiple tags' => [
                'tags' => [
                    14,
                    5,
                ],
                'expected' => 5,
            ],
            'no tags' => [
                'tags' => [],
                'expected' => 10,
            ],
        ];
    }

    /*public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }*/

    /**
     * @dataProvider tagProvider
     *
     * @param array<mixed> $tags
     */
    public function testFilterTags(array $tags, int $expected): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');

        $queryParams = [];
        if (!empty($tags)) {
            $queryParams['filter']['tags'] = $tags;
        }
    
        $url = $urlGenerator->generate('video_games_list');
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
    
        $crawler = $client->request(Request::METHOD_GET, $url);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount($expected, 'article.game-card');

    }
}
