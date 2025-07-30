<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Tag;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function array_fill_callback;

final class TagFixtures extends Fixture 
{
    public function __construct(
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $tags = array_fill_callback(0, 25, fn (int $index): Tag => (new Tag)
            ->setName(sprintf('Tag %d', $index))
        );

        array_walk($tags, [$manager, 'persist']);

        $manager->flush();
    }
}