<?php

namespace App\Repository;

use App\Entity\Continent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Continent>
 *
 * @method Continent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Continent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Continent[]    findAll()
 * @method Continent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContinentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Continent::class);
    }

    public function add(Continent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Continent $entity, bool $flush = false): void
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
        $qb->select(['c.code, c.name, c.active'])
            ->orderBy('c.name', 'ASC') ;
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Continent[] Returns an array of Continent objects
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

//    public function findOneBySomeField($value): ?Continent
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
