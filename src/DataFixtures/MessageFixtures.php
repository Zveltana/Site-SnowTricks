<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($nbrMessage = 0; $nbrMessage <= 18; $nbrMessage++) {
            $message = new Message();
            $message->setUser($this->getReference('user_' . $faker->numberBetween(1, 30)));
            $message->setContent($faker->paragraph(1));
            $message->setCreationDate($faker->dateTime);
            $message->setTrick($this->getReference('trick_' . $faker->numberBetween(1, 16)));

            $manager->persist($message);
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
