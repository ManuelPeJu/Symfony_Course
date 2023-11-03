<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle("The Dark Knight");
        $movie->setReleaseYear(2008);
        $movie->setDescription("This is a description of The Dark Night");
        $movie->setImagePath("https://cdn.pixabay.com/photo/2023/03/14/22/20/relationship-7853278_1280.jpg");
        // Add data to pivot table
        $movie->addActor($this->getReference("actor_1"));
        $movie->addActor($this->getReference("actor_2"));
        //
        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle("Avengers: Endgame");
        $movie2->setReleaseYear(2019);
        $movie2->setDescription("This is a description of Avengers: Endgame");
        $movie2->setImagePath("https://cdn.pixabay.com/photo/2023/06/23/17/12/thor-8083865_1280.jpg");
        $movie2->addActor($this->getReference("actor_3"));
        $movie2->addActor($this->getReference("actor_4"));
        $manager->persist($movie2);

        $manager->flush();
    }
}