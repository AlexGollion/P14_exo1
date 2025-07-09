<?php

namespace App\tests\Functionnal\VideoGame;

use PHPUnit\Framework\TestCase;
use App\Model\Entity\VideoGame;
use App\Model\Entity\Review;
use App\Model\Entity\User;
use App\Rating\RatingHandler;
use App\Rating\CountRatingsPerValue;

class CountRatingsPerValueTest extends TestCase
{
    private $ratingHandler;
    private $videoGame;

    public function setUp(): void
    {
        $this->ratingHandler = new ratingHandler();
        $this->videoGame = new VideoGame();
    }

    public function testCountRatingsPerValue0()
    {
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(0, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
    }

    public function testCountRatingsPerValue() : void
    {
        $data = [
            [5, 5, 'getNumberOfFive'],
            [4, 5, 'getNumberOfFour'],
            [3, 5, 'getNumberOfThree'],
            [2, 5, 'getNumberOfTwo'],
            [1, 5, 'getNumberOfOne']
        ];    

        foreach ($data as $value) {
            for ($i = 0; $i < 5; $i++) {
                $review = (new Review)
                ->setVideoGame($this->videoGame)
                ->setRating($value[0])
                ->setUser(new User());
                
                $this->videoGame->getReviews()->add($review);
            }
        
            $this->ratingHandler->countRatingsPerValue($this->videoGame);
            $this->assertSame($value[1], $this->videoGame->getNumberOfRatingsPerValue()->{$value[2]}());
        }
    }
}