<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use Doctrine\ORM\EntityManager;

final class FilterTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }
    /**
     * @return array<mixed>
     */
    /*public static function tagProvider(): array
    {
        

        return [
            'one tag' => [
                'tags' => [
                    $tags[0]->getId(),
                ],
                'expected' => 4,
            ],
            'multiple tags' => [
                'tags' => [
                    $tags[1]->getId(),
                    $tags[2]->getId(),
                ],
                'expected' => 6,
            ],
            'no tags' => [
                'tags' => [],
                'expected' => 10,
            ],
        ];
    }*/

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
     * @return void
     */
    public function testFilterTags(): void
    {
        $data = $this->generateData();
        $tags = $data['tags'];

        $testData = [
            [
                'tags' => [
                    $tags[0]->getId(),
                ],
                'expected' => 4,
            ],
            [
                'tags' => [
                    $tags[1]->getId(),
                    $tags[2]->getId(),
                ],
                'expected' => 6,
            ],
            [
                'tags' => [],
                'expected' => 10,
            ],
        ];

        $urlGenerator = $this->client->getContainer()->get('router.default');
       
        for($i = 0; $i < count($testData); $i++) {
            $queryParams = [];
            if (!empty($tags)) {
                $queryParams['filter']['tags'] = $testData[$i]['tags'];
            }
        
            $url = $urlGenerator->generate('video_games_list');
            if (!empty($queryParams)) {
                $url .= '?' . http_build_query($queryParams);
            }
        
            $crawler = $this->client->request(Request::METHOD_GET, $url);
    
            $this->assertResponseIsSuccessful();
            $this->assertSelectorCount($testData[$i]['expected'], 'article.game-card');
        }
    }


    /**
     * @return array<mixed>
     */
    public function generateData(): array
    {
        $videoGames = [];
        $tags = [];

        for($i = 0; $i < 3; $i++) {
            $tag = new Tag();
            $tag->setName('tag-' . $i);

            array_push($tags, $tag);
            $this->entityManager->persist($tag);
            $this->entityManager->flush();
        }

        for($i = 0; $i < 10; $i++) {
            $videoGame = new VideoGame();
            $videoGame->setTitle('jeu-video-' . $i);
            $videoGame->setDescription("test");
            $videoGame->setReleaseDate(new \DateTimeImmutable());
            $videoGame->setTest("test");
            $videoGame->setRating(3);

            if ($i < 4) {
                $videoGame->getTags()->add($tags[0]);
            } else {
                $videoGame->getTags()->add($tags[1]);
                $videoGame->getTags()->add($tags[2]);
            }

            array_push($videoGames, $videoGame);
            $this->entityManager->persist($videoGame);
            $this->entityManager->flush();
        }

        return ["videoGames" => $videoGames, "tags" => $tags];
    }
}