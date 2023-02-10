<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 *
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function add(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Country $entity, bool $flush = false): void
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
        $alias = 'c';
        $qb = $this->createQueryBuilder($alias);
        if($criteria) {
            $i = 1;
            foreach ($criteria as $name => $value) {
                $qb->andWhere($alias. "." . $name . " = :val" . $i);
                $qb->setParameter(":val" . $i, $value);
                $i++;
            }
        }
        $qb->innerJoin('c.continent', 'cont');
        $qb->select(['c.code, c.name, c.phone_code, c.capital, c.active, cont.name as continent_name']);
        $qb->orderBy('c.code', 'ASC');
        return $qb->getQuery()->getResult();
    }


   /* public function searchCurrencies() :array{
        $qb = $this->createQueryBuilder('c')
            ->select('c.currency_code')
            ->distinct();
        $res = $qb->getQuery()->getSingleColumnResult('currency_code');
        return array_combine($res, $res);
    }*/

//    /**
//     * @return Country[] Returns an array of Country objects
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

//    public function findOneBySomeField($value): ?Country
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
