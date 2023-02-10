<?php

namespace App\Repository;

use App\Entity\City;
use App\Service\Helpers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<City>
 *
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, City::class);
    }

    public function add(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(City $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * Returning Results as an associative Array
     * @return array
     */
    public function search($criteria = null): array
    {
        $qb = $this->createQueryBuilder('c');

        if($criteria) {
            $i = 1;
            foreach ($criteria as $filterName => $filterValue) {
                $qb->andWhere("c." . $filterName . " = :val" . $i);
                $qb->setParameter(":val" . $i, $filterValue);
                $i++;
            }
        }
        $qb->innerJoin('c.country', 'country');
        $qb->select(['c.id, c.name, c.active, country.name as country_name']);
        $qb->orderBy('c.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return City[] Returns an array of City objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?City
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
