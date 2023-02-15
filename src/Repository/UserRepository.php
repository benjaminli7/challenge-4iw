<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    public function findUsersByRole($role)
    {
        return $this->createQueryBuilder('u')
        ->andWhere('JSON_GET_TEXT(u.roles,0) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,1) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,2) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,3) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,4) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,5) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,6) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,7) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,8) = :role')
        ->orWhere('JSON_GET_TEXT(u.roles,9) = :role')
        ->setParameter('role', $role)
        ->getQuery()
        ->getResult();
    
        // return $this->createQueryBuilder('u')
        //     ->where("JSON_EXTRACT(u.roles, '$[*]') = :role")
        //     ->setParameter('role', $role)
        //     ->getQuery()
        //     ->getResult()
        //     ;
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
