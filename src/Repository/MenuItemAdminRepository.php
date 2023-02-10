<?php

namespace App\Repository;

use App\Entity\MenuItemAdmin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuItemAdmin>
 *
 * @method MenuItemAdmin|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuItemAdmin|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuItemAdmin[]    findAll()
 * @method MenuItemAdmin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuItemAdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItemAdmin::class);
    }

    public function add(MenuItemAdmin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MenuItemAdmin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    /**
     * @return MenuItemAdmin[]
     */
    public function find_menuAdmin(): array
    {
        return $this->createQueryBuilder('mi')
            ->join('mi.module' , 'mod')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return MenuItemAdmin[] Returns an array of MenuItemAdmin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MenuItemAdmin
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function find_innerJoin($profile_id): array{
        return $this->createQueryBuilder('mi')

            ->leftJoin('mi.module' , 'mod')

            ->select(['mi.id', 'mi.title', 'mi.route', 'mi.parent'])

            ->orderBy('mi.displayOrder' , 'ASC' )
            ->getQuery()
            ->getResult()
            ;
    }
}
