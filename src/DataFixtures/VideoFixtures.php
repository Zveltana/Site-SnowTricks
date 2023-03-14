<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $videoTrick = [
            'https://www.youtube.com/embed/L4bIunv8fHM',
            'https://www.youtube.com/embed/kOyCsY4rBH0',
            'https://www.youtube.com/embed/SFYYzy0UF-8',
            'https://www.youtube.com/embed/CSfXm2h7oTU',
            'https://www.youtube.com/embed/Sj7CJH9YvAo',
            'https://www.youtube.com/embed/SlhGVnFPTDE',
            'https://www.youtube.com/embed/slWCAZijWTI',
            'https://www.youtube.com/embed/I7v_OHSbvj0',
            'https://www.youtube.com/embed/0XfrQ8xZQpY',
            'https://www.youtube.com/embed/cGiAFk2adMw'
        ];

        for ($nbrVideo = 1; $nbrVideo <= 10; $nbrVideo++) {
            $video = new Video();
            $video->setVideo($faker->randomElement($videoTrick));
            $video->setTrick($this->getReference('trick_' . $faker->numberBetween(1, 16)));

            $manager->persist($video);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TrickFixtures::class
        ];
    }
}