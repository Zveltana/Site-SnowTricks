<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $trick = [
            1 => [
                'name' => 'Mute',
                'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                'category' => $this->getReference('category_1'),
                'cover' => 'trick-1.jpg'
            ],
            2 => [
                'name' => 'Style week',
                'description' => 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.',
                'category' => $this->getReference('category_1'),
                'cover' => 'trick-2.jpg'
            ],
            3 => [
                'name' => 'Indy',
                'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
                'category' => $this->getReference('category_1'),
                'cover' => 'trick-3.jpg'
            ],
            4 => [
                'name' => 'Stalefish',
                'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.',
                'category' => $this->getReference('category_1'),
                'cover' => 'trick-4.jpg'
            ],
            5 => [
                'name' => 'Japan air',
                'description' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                'category' => $this->getReference('category_2'),
                'cover' => 'trick-5.jpg'
            ],
            6 => [
                'name' => 'Backside Air',
                'description' => 'Mais en fait, pourquoi le Backside air est-il aussi emblématique? C’est vrai quoi, il 
                                  existe des tricks bien plus compliqués que ça dans le snowboard moderne, d’autres 
                                  aussi avec des noms bien plus amusants… Mais rappelle-toi: le backside air est le 
                                  seul trick que tu ne peux pas faire en ski – déjà ça pose. Ensuite, c’est sans doute 
                                  le trick qui marque le plus ta personnalité, car il y a 10.000 manières de le faire. 
                                  Enfin, pour un trick “simple”, il est tout de même assez technique. Il faut l’envoyer 
                                  en avançant le buste au pop, et vraiment s’engager dans les airs pour pouvoir bien 
                                  grabber comme il se doit. Voilà à notre avis trois raisons majeures à ce succès du 
                                  backside air, toutes générations et tous pratiquants confondus.',
                'category' => $this->getReference('category_2'),
                'cover' => 'trick-6.jpg'
            ],
        ];

        foreach ($trick as $key => $value) {
            $trick = new Trick();
            $trick->setName($value['name']);
            $trick->setDescription($value['description']);
            $trick->setCategory($value['category']);
            $trick->setCreationDate(new \DateTimeImmutable());
            $trick->setCover($value['cover']);

            $manager->persist($trick);

            $this->addReference('trick_'. $key, $trick);
        }

        $faker = Factory::create('fr_FR');

        for ($i = 7; $i <= 16; $i++) {
            $trick = new Trick();
            $trick->setName($faker->words(3, true));
            $trick->setDescription($faker->text(500));
            $trick->setCategory($this->getReference('category_' . $faker->numberBetween(1, 2)));
            $trick->setCreationDate(new \DateTimeImmutable());
            $trick->setCover('trick-' . $faker->numberBetween(1, 11) . '.jpg');

            $manager->persist($trick);

            $this->addReference('trick_' . $i, $trick);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }
}
