<?php

namespace App\Repository;

use App\Entity\OrderArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderArticle>
 *
 * @method OrderArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderArticle[]    findAll()
 * @method OrderArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderArticle::class);
    }

    public function save(OrderArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMostOrderedArticleByDate($date)
    {
        return $this->createQueryBuilder('o')
            ->select('o.id, SUM(o.quantity) as total')
            ->andWhere('o.date = :date')
            ->setParameter('date', $date)
            ->groupBy('o.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return OrderArticle[] Returns an array of OrderArticle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrderArticle
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findBestSeller()
    {
           return $this->createQueryBuilder('o')
                ->select('o.id, SUM(o.quantity) as total')
                ->groupBy('o.id')
                ->orderBy('total', 'DESC')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult()
        ;
    }
}
