<?php

namespace App\EventListener;

use App\Entity\Article;	
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use Doctrine\ORM\Events;

class ArticleListener implements EventSubscriberInterface
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

        // Only apply this listener to Article entities
        if (!$entity instanceof Article) {
            return;
        }

        // Check if the Article entity is associated with any Order entities
        $orders = $entity->getOrders();
        if (!empty($orders)) {
            throw new \Exception('Impossible de supprimer un article qui est lié à une commande..');
        }
    }
}
