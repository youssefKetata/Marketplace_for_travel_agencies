<?php

namespace App\Repository;

use App\Entity\ApiProductClick;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiProductClick>
 *
 * @method ApiProductClick|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiProductClick|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiProductClick[]    findAll()
 * @method ApiProductClick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiProductClickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiProductClick::class);
    }

    public function save(ApiProductClick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiProductClick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByApiProductAndDate($apiProduct, DateTime $date)
    {
        $startOfDay = clone $date;
        $startOfDay->setTime(0, 0, 0);

        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);

        return $this->createQueryBuilder('apc')
            ->andWhere('apc.apiProduct = :apiProduct')
            ->andWhere('apc.date >= :startOfDay')
            ->andWhere('apc.date <= :endOfDay')
            ->setParameter('apiProduct', $apiProduct)
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?ApiProductClick
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
