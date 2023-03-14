<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PictureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $pictureUrl = [
            'trick-3.jpg',
            'trick-4.jpg',
            'trick-5.jpg',
            'trick-6.jpg',
            'trick-7.jpg',
            'trick-8.jpg',
            'trick-9.jpg',
            'trick-10.jpg',
            'trick-11.jpg',
            'trick-12.jpg',
            'trick-13.jpg',
            'trick-14.jpg',
            'trick-15.jpg',
            'trick-16.jpg',
            'trick-17.jpg',
            'trick-18.jpg',
            'trick-19.jpg',
            'trick-20.jpg',
        ];

        for ($nbrPicture = 3; $nbrPicture <= 20; $nbrPicture++) {
            $picture = new Picture();
            $picture->setPicture($faker->randomElement($pictureUrl));
            $picture->setAlt($faker->paragraph(1));
            $picture->setTrick($this->getReference('trick_' . $faker->numberBetween(1, 2)));

            $manager->persist($picture);
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
