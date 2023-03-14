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
            'https://youtu.be/L4bIunv8fHM',
            'https://youtu.be/kOyCsY4rBH0',
            'https://youtu.be/SFYYzy0UF-8',
            'https://youtu.be/CSfXm2h7oTU',
            'https://youtu.be/Sj7CJH9YvAo',
            'https://youtu.be/SlhGVnFPTDE',
            'https://youtu.be/slWCAZijWTI',
            'https://youtu.be/I7v_OHSbvj0',
            'https://youtu.be/0XfrQ8xZQpY',
            'https://youtu.be/cGiAFk2adMw'
        ];

        for ($nbrVideo = 1; $nbrVideo <= 10; $nbrVideo++) {
            $video = new Video();
            $video->setVideo($faker->randomElement($videoTrick));
            $video->setTrick($this->getReference('trick_' . $faker->numberBetween(1, 2)));

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