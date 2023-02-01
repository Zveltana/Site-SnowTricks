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
        $pictureMale = [
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            '11',
            '12',
            '13',
            '14',
            '15',
        ];

        $pictureFemale = [
            '16',
            '17',
            '18',
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
            '28',
            '29',
            '30',
        ];

        for ($nbUsers = 1; $nbUsers <= 30; $nbUsers++) {
            $user = new User();

            $gender = $faker->randomElement($genders);

            $picture = 'user+';
            $pictureIdMale = $faker->randomElement($pictureMale) . '.jpg';
            $pictureIdFemale = $faker->randomElement($pictureFemale) . '.jpg';

            $picture .= ($gender === 'male' ? $pictureIdMale : $pictureIdFemale);

            $user->setName($faker->name($gender));
            $user->setPicture($picture);
            $user->setEmail(sprintf('user+%d@gmail.com', $nbUsers));
            $user->setPassword($this->encoder->hashPassword($user, 'password'));
            $user->setIsVerified($faker->numberBetween(0, 1));
            $manager->persist($user);

            $this->addReference('user_'. $nbUsers, $user);
        }

        $manager->flush();
    }
}
