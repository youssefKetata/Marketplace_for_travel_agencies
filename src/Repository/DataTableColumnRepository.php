<?php

namespace App\Repository;

use App\Entity\DataTableColumn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataTableColumn>
 *
 * @method DataTableColumn|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataTableColumn|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataTableColumn[]    findAll()
 * @method DataTableColumn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataTableColumnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataTableColumn::class);
    }

    public function add(DataTableColumn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DataTableColumn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    /**
     * @return DataTableColumn[] Returns an array of DataTableColumn
     */
    public function findVisibleColmunsByTable($nomTable): array
    {
        //TODO a corriger et penser à la factorisation !!!!
        return $this->createQueryBuilder('m')
            ->andWhere('m.nomTable = :val')
            ->setParameter('val', $nomTable)
            ->select('upper(m.nomColonne) as name, m.selector, m.sortable, m.width, m.state')
            ->andWhere('m.visible = 1')
            ->orderBy('m.display_order', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }



//    /**
//     * @return DataTableColumn[] Returns an array of DataTableColumn objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DataTableColumn
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
