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

        $article1 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Coca']);
        $article2 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Fanta']);
        $article3 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Sprite']);
        $article4 = $manager->getRepository(Article::class)->findOneBy(['name' => 'Eau']);
        
        $order1 = (new Order())
            ->setStatus('pending')
            ->setDate(new \DateTime())
            ->setClient($user1)
            ->addArticle($article1)
            ->addArticle($article2)
        ;

        $manager->persist($order1);

        $order2 = (new Order())
            ->setStatus('pending')
            ->setDate(new \DateTime())
            ->setClient($user2)
            ->addArticle($article3)
            ->addArticle($article4)
        ;
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
