<?php

namespace App\tests\Functionnal\VideoGame;

use PHPUnit\Framework\TestCase;
use App\Model\Entity\VideoGame;
use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Rating\RatingHandler;
use App\Rating\CountRatingsPerValue;

class CalculateAverageRatingTest extends TestCase
{
    private $ratingHandler;
    private $videoGame;

    public function setUp(): void
    {
        $this->ratingHandler = new ratingHandler();
        $this->videoGame = new VideoGame();
    }
    public function testAverageRatingNull()
    {
        $this->ratingHandler->calculateAverage($this->videoGame);
        $this->assertSame(null, $this->videoGame->getAverageRating());

    }
    
    public function testAverageRatingValue()
    {
        for ($i = 0; $i < 5; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(3)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->calculateAverage($this->videoGame);
        $this->assertSame(3, $this->videoGame->getAverageRating());
    }
}