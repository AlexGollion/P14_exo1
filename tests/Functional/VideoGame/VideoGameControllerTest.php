<?php

namespace App\Tests\Functional\VideoGame;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Model\Entity\User;

class VideoGameControllerTest extends WebTestCase
{
    
    public function testRateVideoGame() 
    {
        $client = static::createClient();

        $userRepository = $client->getContainer()->get('doctrine')->getManager()->getRepository(User::class);
        $user = $userRepository->findOneByEmail('user+0@email.com');
        $client->loginUser($user);
        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']));

        $form = $crawler->selectButton('Poster')->form();
        $form['review[rating]'] = 5;
        $form['review[comment]'] = "test";
        $client->submit($form);
        
        $this->assertTrue($client->getResponse()->isRedirect());
        
        $this->entityManager = $client->getContainer()->get('doctrine')->getManager();
        $reviewRepository = $this->entityManager->getRepository('App\Model\Entity\Review');
        $reviews = $reviewRepository->findBy(['comment' => 'test']);
        $this->assertGreaterThan(0, count($reviews), 'Review was not created');
    }

    public function testRateVideoGameError()
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
                'comment' => 'test'
            ],
            [
                'rating' => 6,
                'comment' => ""
            ]
        ];

        foreach ($data as $key => $value) {
            $this->submitForm($form, $client, $value, $csrfToken, $urlGenerator);
        }
    }

    private function submitForm($form, $client, $data, $csrfToken, $urlGenerator)
    {
        $client->request('POST', $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']), [
            'review' => [
                'rating' => $data['rating'], // Invalid value
                'comment' => $data['comment'],
                '_token' => $csrfToken // Include CSRF token if your form uses it
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(422);
    }

    public function testRateVideoGameWithoutLogin()
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router.default');

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_show', ['slug' => 'jeu-video-4']));

        $this->assertSelectorNotExists('button:contains("Poster")');
    }
}