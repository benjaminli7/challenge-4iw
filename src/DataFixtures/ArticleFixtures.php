<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $object = '{"Boissons": ["Coca", "Fanta", "Sprite", "Eau", "Jus de fruit"], "Sides": ["Frites", "Salade", "Pommes de terre"]}';
        $object = json_decode($object, true);
        
        foreach($object as $category => $articles) {
            $category = (new Category())
                ->setName($category)
                ;
            $manager->persist($category);
            foreach($articles as $article) {
                $article = (new Article())
                    ->setName($article)
                    ->setCategory($category)
                    ->setPrice(rand(1, 10))
                    ;
                $manager->persist($article);
            };
        }

        $manager->flush();
    }
}