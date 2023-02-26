<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $tags = ["Vegan", "Sans gluten", "Best seller"];

        foreach ($tags as $tagName) {
            $tag = (new Tag())
                ->setName($tagName)
                ->setColor($faker->hexColor())
            ;

            $manager->persist($tag);
        }

        $manager->flush();
    }
}
