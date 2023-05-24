<?php

namespace App\Repository;

use App\Entity\ApiProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiProduct>
 *
 * @method ApiProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiProduct[]    findAll()
 * @method ApiProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiProduct::class);
    }

    public function save(ApiProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ApiProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneByTwoFields($field1, $field2)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder->where('a.api = :field1')
            ->andWhere('a.idProductFromApi = :field2')
            ->setParameter('field1', $field1)
            ->setParameter('field2', $field2);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }


//    /**
//     * @return ApiProduct[] Returns an array of ApiProduct objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ApiProduct
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
