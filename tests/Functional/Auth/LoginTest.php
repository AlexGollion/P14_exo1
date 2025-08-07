<?php

declare(strict_types=1);

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Doctrine\ORM\EntityManager;
use App\Model\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

final class LoginTest extends FunctionalTestCase
{
    protected KernelBrowser $client;
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }

    public function testThatLoginShouldSucceeded(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPlainPassword('password');
        $user->setUsername('test');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->get('/auth/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);

        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->get('/auth/logout');

        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    public function testThatLoginShouldFailed(): void
    {
        $this->get('/auth/login');

        $this->client->submitForm('Se connecter', [
            'email' => 'user+1@email.com',
            'password' => 'fail',
        ]);

        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);

        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }
}
