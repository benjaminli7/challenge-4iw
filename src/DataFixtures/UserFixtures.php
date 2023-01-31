<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $pwd = 'test';

        for ($i=0; $i<10; $i++) {
            $user = (new User())
                ->setEmail($i . 'client@user.fr')
                ->setPlainPassword($pwd)
                ->setRoles(['ROLE_CLIENT'])
            ;
            $manager->persist($user);
        }


        $user = (new User())
            ->setEmail('manager@user.fr')
            ->setPlainPassword($pwd)
            ->setRoles(['ROLE_MANAGER'])
        ;
        $manager->persist($user);

        $user = (new User())
            ->setEmail('employee@user.fr')
            ->setPlainPassword($pwd)
            ->setRoles(['ROLE_EMPLOYEE'])
        ;
        $manager->persist($user);

        $manager->flush();
    }
}