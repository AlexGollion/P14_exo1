<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Model\Entity\Tag;

final class FilterTest extends WebTestCase
{
    /**
     * @return array<mixed>
     */
    public static function tagProvider(): array
    {
        return [
            "one tag" => [
                "tags" => [
                    15
                ],
                "expected" => 6
            ],
            "multiple tags" => [
                "tags" => [
                    14,
                    5,
                ],
                "expected" => 5
            ],
            "no tags" => [
                "tags" => [],
                "expected" => 10
            ]
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
    * @param array<mixed> $tags
    * @param int $expected
    */
    public function testFilterTags(array $tags, int $expected): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_list'));

        $form = $crawler->selectButton('Filtrer')->form();

        foreach ($tags as $tag) {
           $checkboxes = $form->get("filter[tags]");
           if (is_array($checkboxes)) {
               foreach ($checkboxes as $checkbox) {
                    $value = $checkbox->availableOptionValues(); 
                    if ($value[0] == $tag) {
                       $checkbox->tick();
                    }    
               }
           }
        }
        

        $client->submit($form);
        $this->assertSelectorCount($expected, 'article.game-card');
    }
}
