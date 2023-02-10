<?php

namespace App\Repository;

use App\Entity\FileData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FileData>
 *
 * @method FileData|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileData|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileData[]    findAll()
 * @method FileData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileData::class);
    }

    public function add(FileData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FileData $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function  deleteFile(FileData $entity ){
        if(file_exists($entity->getDirectoryPath())){
            try {
                $delete  = unlink($entity->getDirectoryPath().'/'.$entity->getName());
                return $delete;
            }catch (\Exception $e){
                throw new \Exception($e->getMessage());
            }
        }else{
            throw new \Exception('file not found');
        }

    }
//    /**
//     * @return FileData[] Returns an array of FileData objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FileData
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
