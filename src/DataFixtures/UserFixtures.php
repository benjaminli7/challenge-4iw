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
                ->setFirstname("Client" . $i . "_firstname")
                ->setLastname("Client" . $i . "_lastname")
                ->setRoles(['ROLE_CLIENT'])
                ->setIsVerified(true)
                ->setPhone('0606060606')
            ;
            $manager->persist($user);

        }
        $user = (new User())
            ->setEmail('manager@user.fr')
            ->setPlainPassword("toto123")
            ->setFirstname("Admin")
            ->setLastname("ESGI")
            ->setRoles(['ROLE_MANAGER'])
            ->setIsVerified(true)
            ->setPhone('0606060606')
        ;
        $manager->persist($user);

        $user = (new User())
            ->setEmail('employee@user.fr')
            ->setPlainPassword("dodo123")
            ->setFirstname("Employee")
            ->setLastname("ESGI")
            ->setRoles(['ROLE_EMPLOYEE'])
            ->setIsVerified(true)
            ->setPhone('0606060606')
        ;
        $manager->persist($user);

        $manager->flush();
    }
}