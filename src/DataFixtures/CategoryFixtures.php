<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = (new Category())->setName('Grabs');
        $manager->persist($category);

        $this->addReference('category_1', $category);

        $category = (new Category())->setName('Old School');
        $manager->persist($category);

        $this->addReference('category_2', $category);


        $manager->flush();
    }
}
