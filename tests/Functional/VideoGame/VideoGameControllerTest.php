<?php

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoGameControllerTest extends WebTestCase
{
    private EntityManager $entityManager;

    public function testRateVideoGame(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $user = $userRepository->findOneByEmail('user+0@email.com');
        $client->loginUser($user);
        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']));

        $form = $crawler->selectButton('Poster')->form();
        $form['review[rating]'] = "5";
        $form['review[comment]'] = 'test';
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect());

        $this->entityManager = $client->getContainer()->get('doctrine')->getManager();
        $reviewRepository = $this->entityManager->getRepository('App\Model\Entity\Review');
        $reviews = $reviewRepository->findBy(['comment' => 'test']);
        $this->assertGreaterThan(0, count($reviews), 'Review was not created');
    }

    public function testRateVideoGameError(): void
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $user = $userRepository->findOneByEmail('user+1@email.com');
        $client->loginUser($user);
        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']));

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
            $this->submitForm($client, $value, $csrfToken, $urlGenerator);
        }
    }

    /**
     * @param array{rating: int, comment: string} $data
     */
    private function submitForm(KernelBrowser $client, array $data, string $csrfToken, UrlGeneratorInterface $urlGenerator): void
    {
        $client->request('POST', $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']), [
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
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']));

        $this->assertSelectorNotExists('button:contains("Poster")');
    }
}
