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
                ->setFirstname("client" . $i . "_firstname")
                ->setLastname("client" . $i . "_lastname")
                ->setRoles(['ROLE_CLIENT'])
                ->setIsVerified(true)
            ;
            $manager->persist($user);
        }


        $user = (new User())
            ->setEmail('manager@user.fr')
            ->setPlainPassword($pwd)
            ->setFirstname("manager_firstname")
            ->setLastname("manager_lastname")
            ->setRoles(['ROLE_MANAGER'])
            ->setIsVerified(true)
        ;
        $manager->persist($user);

        $user = (new User())
            ->setEmail('employee@user.fr')
            ->setPlainPassword($pwd)
            ->setFirstname("employee_firstname")
            ->setLastname("employee_lastname")
            ->setRoles(['ROLE_EMPLOYEE'])
            ->setIsVerified(true)
        ;
        $manager->persist($user);

        $manager->flush();
    }
}