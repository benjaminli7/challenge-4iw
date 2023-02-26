<?php

namespace App\EventListener;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use Doctrine\ORM\Events;

class CategoryListener implements EventSubscriberInterface
{
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Category) {
            return;
        }

        $articles = $entity->getArticles();
        foreach($articles as $article) {
            $orders = $article->getOrders();
            if (!empty($orders)) {
                throw new \Exception('Impossible de supprimer une catégorie qui est liée à une commande..');
            }
        }
    }
}
