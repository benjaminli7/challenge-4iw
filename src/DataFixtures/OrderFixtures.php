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

        $user1 = $manager->getRepository(User::class)->findOneBy(['email' => '0client@user.fr']);
        $user2 = $manager->getRepository(User::class)->findOneBy(['email' => '1client@user.fr']);
        $employee1 = $manager->getRepository(User::class)->findOneBy(['email' => 'employee@user.fr']);

        $article1 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Coca']);
        $article2 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Fanta']);
        $article3 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Sprite']);
        $article4 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Eau']);
        
        $order1 = (new Order())
            ->setStatus('ONGOING')
            ->setDate(new \DateTime())
            ->setClient($user1)
            ->addArticle($article1, 2)
            ->addArticle($article2, 3)
            ->setEmployee($employee1)
        ;

        $articles = $order1->getOrderArticles()->getValues();
        $totalPrice = 0;

        foreach ($articles as $article) {
            $article->getArticle()->setOrderCount($article->getQuantity());
            $totalPrice += $article->getArticle()->getPrice() * $article->getQuantity();
        }
        $order1->setTotalPrice($totalPrice);

        $manager->persist($order1);

        $order2 = (new Order())
            ->setStatus('ONGOING')
            ->setDate(new \DateTime())
            ->setClient($user2)
            ->addArticle($article3, 1)
            ->addArticle($article4, 2)
            ->setEmployee($employee1)
        ;

        $articles = $order2->getOrderArticles()->getValues();
        $totalPrice = 0;

        foreach ($articles as $article) {
            $article->getArticle()->setOrderCount($article->getQuantity());
            $totalPrice += $article->getArticle()->getPrice() * $article->getQuantity();
        }
        $order2->setTotalPrice($totalPrice);

        $manager->persist($order2);

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
