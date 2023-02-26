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
        $object = '{"Plats": ["Classic burger", "BBQ burger", "Pad Thai", "Steak frites", "Lasagne", "Tacos", "Sushi", "Salade césar"],"Boissons": ["Heineken", "Tsingtao", "Budweiser", "Bud Light", "Thé glacé pêche", "Margarita", "Martini", "Jus d\'orange", "Jus de pomme", "Coca", "Fanta", "Sprite", "Eau"], "Sides": ["Frites de patate douce", "Salade", "Purée de pommes de terre", "Riz", "Onion rings", "Coleslaw", "Macaroni & Cheese"], "Desserts": ["Tiramisu", "Macarons chocolat","Paris-Brest", "Crème brûlée", "Brownie", "Cheesecake", "Glace", "Mousse au chocolat"]}';
        $object = json_decode($object, true);

        foreach ($object as $category => $articles) {
            $category = (new Category())
                ->setName($category);
            $manager->persist($category);
            foreach ($articles as $article) {
                $article = (new Article())
                    ->setName($article)
                    ->setCategory($category)
                    ->setPrice(rand(1, 10));
                $manager->persist($article);
            };
        }

        $manager->flush();
    }
}
