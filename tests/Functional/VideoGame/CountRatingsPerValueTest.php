<?php

namespace App\tests\Functionnal\VideoGame;

use phpunit\Framework\TestCase;
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
    
    public function testCountRatingsPerValue1()
    {
        for ($i = 0; $i < 3; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(1)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(3, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
    }
    public function testCountRatingsPerValue2()
    {
        for ($i = 0; $i < 5; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(2)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(5, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
    }
    public function testCountRatingsPerValue3()
    {
        for ($i = 0; $i < 7; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(3)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(7, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
    }
    public function testCountRatingsPerValue4()
    {
        for ($i = 0; $i < 1; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(4)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(1, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
    }
    public function testCountRatingsPerValue5()
    {
        for ($i = 0; $i < 10; $i++) {
            $review = (new Review)
            ->setVideoGame($this->videoGame)
            ->setRating(5)
            ->setUser(new User());
            
            $this->videoGame->getReviews()->add($review);
        }
    
        $this->ratingHandler->countRatingsPerValue($this->videoGame);
        $this->assertSame(10, $this->videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }
}