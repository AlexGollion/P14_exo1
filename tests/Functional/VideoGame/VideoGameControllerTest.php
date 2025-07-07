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

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('video_games_list'));

        $form = $crawler->selectButton('py-2')->form();
        $form['review[rating]'] = 5;
        $form['review[comment]'] = "test";
        $client->submit($form);
        echo $client->getResponse()->getContent();
    }
}