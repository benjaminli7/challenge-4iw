<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $articles = $manager->getRepository(Article::class)->findAll();
        $user = $manager->getRepository(User::class)->findAll();
        $employee1 = $manager->getRepository(User::class)->findOneBy(['email' => 'employee@user.fr']);

        for($i = 0; $i < 10; $i++) {
            $randomArticle = $articles[array_rand($articles, 1)];
            $randomArticle2 = $articles[array_rand($articles, 1)];
            $randomArticle3 = $articles[array_rand($articles, 1)];
            $randomUser = $user[array_rand($user, 1)];

            $order = (new Order())
                ->setStatus('ONGOING')
                ->setDate(new \DateTime())
                ->setClient($randomUser)
                ->addArticle($randomArticle, rand(1, 3))
                ->addArticle($randomArticle2, rand(1, 3))
                ->addArticle($randomArticle3, rand(1, 3))
                ->setEmployee($employee1)
            ;

            $totalPrice = 0;
            $order_articles = $order->getOrderArticles()->getValues();
            foreach ($order_articles as $order_article) {
                $order_article->getArticle()->setOrderCount($order_article->getQuantity());
                $totalPrice += $order_article->getArticle()->getPrice() * $order_article->getQuantity();
            }
            $order->setTotalPrice($totalPrice);

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ArticleFixtures::class,
        ];
    }
}
