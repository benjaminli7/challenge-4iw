<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use Doctrine\ORM\Events;

class UserListener implements EventSubscriberInterface
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

        if (!$entity instanceof User) {
            return;
        }

        $orders = $entity->getOrders();

        if (!empty($orders)) {
            throw new \Exception('Vous ne pouvez pas supprimer un client qui a des commandes.');
        }
    }
}
