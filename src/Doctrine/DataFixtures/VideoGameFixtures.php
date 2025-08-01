<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\CalculateAverageRating;
use App\Rating\CountRatingsPerValue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(Tag::class)->findAll();

        $users = $manager->getRepository(User::class)->findAll();

        $videoGames = \array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame())
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new \DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        // TODO : Ajouter les tags aux vidéos
        array_walk($videoGames, static function (VideoGame $videoGame) use ($tags): void {
            for ($i = 0; $i < 5; ++$i) {
                $videoGame->getTags()->add($tags[rand(0, count($tags) - 1)]);
            }
        });

        array_walk($videoGames, [$manager, 'persist']);

        // TODO : Ajouter des reviews aux vidéos
        array_walk($videoGames, function (VideoGame $videoGame) use ($users, $manager): void {
            for ($i = 0; $i < rand(0, 5); ++$i) {
                $review = (new Review())
                ->setUser($users[rand(0, count($users) - 1)])
                ->setVideoGame($videoGame)
                ->setRating(rand(1, 5))
                ->setComment($this->faker->paragraphs(6, true));

                $videoGame->getReviews()->add($review);

                $manager->persist($review);

                $this->calculateAverageRating->calculateAverage($videoGame);
                $this->countRatingsPerValue->countRatingsPerValue($videoGame);
            }
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, TagFixtures::class];
    }
}
