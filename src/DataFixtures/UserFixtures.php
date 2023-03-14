<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        $genders = ['male', 'female'];

        for ($nbUsers = 1; $nbUsers <= 30; $nbUsers++) {
            $user = new User();

            $gender = $faker->randomElement($genders);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';

            $picture .= ($gender === 'male' ? 'men/' : 'women/') . $pictureId;

            $user->setName($faker->name($gender));
            $user->setPicture($picture);
            $user->setEmail(sprintf('user+%d@email.com', $nbUsers));
            $user->setPassword($this->encoder->hashPassword($user, 'password'));
            $user->setIsVerified($faker->numberBetween(0, 1));
            $manager->persist($user);

            $this->addReference('user_'. $nbUsers, $user);
        }

        $manager->flush();
    }
}
