<?php

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class VideoGameControllerTest extends WebTestCase
{
    private EntityManager $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        if ($this->entityManager->isOpen()) {
            $this->entityManager->close();
        }
    }

    public function testRateVideoGame(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPlainPassword('password');
        $user->setUsername('test');

        $videoGame = new VideoGame();
        $videoGame->setTitle('jeu-video-4');
        $videoGame->setDescription("test");
        $videoGame->setReleaseDate(new \DateTimeImmutable());
        $videoGame->setTest("test");
        $videoGame->setRating(3);

        
        $this->entityManager->persist($user);
        $this->entityManager->persist($videoGame);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $urlGenerator = $this->client->getContainer()->get('router.default');

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => $videoGame->getSlug()]));

        $form = $crawler->selectButton('Poster')->form();
        $form['review[rating]'] = "5";
        $form['review[comment]'] = 'test';
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $reviewRepository = $this->entityManager->getRepository('App\Model\Entity\Review');
        $reviews = $reviewRepository->findBy(['comment' => 'test']);
        $this->assertGreaterThan(0, count($reviews), 'Review was not created');
    }

    public function testRateVideoGameError(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPlainPassword('password');
        $user->setUsername('test');

        $videoGame = new VideoGame();
        $videoGame->setTitle('jeu-video-4');
        $videoGame->setDescription("test");
        $videoGame->setReleaseDate(new \DateTimeImmutable());
        $videoGame->setTest("test");
        $videoGame->setRating(3);

        $this->entityManager->persist($user);
        $this->entityManager->persist($videoGame);
        $this->entityManager->flush();

        $this->client->loginUser($user);
        $urlGenerator = $this->client->getContainer()->get('router.default');

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => $videoGame->getSlug()]));

        $form = $crawler->selectButton('Poster')->form();

        $csrfToken = $form['review[_token]']->getValue(); // Adjust field name as needed

        $data = [
            [
                'rating' => 6,
                'comment' => 'test',
            ],
            [
                'rating' => 6,
                'comment' => '',
            ],
        ];

        foreach ($data as $key => $value) {
            $this->submitForm($value, $csrfToken, $urlGenerator, $videoGame);
        }
    }

    /**
     * @param array{rating: int, comment: string} $data
     */
    private function submitForm(array $data, string $csrfToken, UrlGeneratorInterface $urlGenerator, VideoGame $videoGame): void
    {
        $this->client->request('POST', $urlGenerator->generate('video_games_show', ['slug' => $videoGame->getSlug()]), [
            'review' => [
                'rating' => $data['rating'], // Invalid value
                'comment' => $data['comment'],
                '_token' => $csrfToken, // Include CSRF token if your form uses it
            ],
        ]);

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRateVideoGameWithoutLogin(): void
    {
        $videoGame = new VideoGame();
        $videoGame->setTitle('jeu-video-4');
        $videoGame->setDescription("test");
        $videoGame->setReleaseDate(new \DateTimeImmutable());
        $videoGame->setTest("test");
        $videoGame->setRating(3);

        $this->entityManager->persist($videoGame);
        $this->entityManager->flush();

        $urlGenerator = $this->client->getContainer()->get('router.default');

        $crawler = $this->client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => $videoGame->getSlug()]));

        $this->assertSelectorNotExists('button:contains("Poster")');
    }
}
