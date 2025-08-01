<?php

namespace App\tests\Functionnal\VideoGame;

use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CalculateAverageRatingTest extends TestCase
{
    private RatingHandler $ratingHandler;
    private VideoGame $videoGame;

    public function setUp(): void
    {
        $this->ratingHandler = new RatingHandler();
        $this->videoGame = new VideoGame();
    }

    public function testAverageRatingNull(): void
    {
        $this->ratingHandler->calculateAverage($this->videoGame);
        $this->assertSame(null, $this->videoGame->getAverageRating());
    }

    public function testAverageRatingValue(): void
    {
        for ($i = 0; $i < 5; ++$i) {
            $review = (new Review())
            ->setVideoGame($this->videoGame)
            ->setRating(3)
            ->setUser(new User());

            $this->videoGame->getReviews()->add($review);
        }

        $this->ratingHandler->calculateAverage($this->videoGame);
        $this->assertSame(3, $this->videoGame->getAverageRating());
    }
}
