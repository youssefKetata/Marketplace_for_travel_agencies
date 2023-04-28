<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function add(User $entity, bool $flush = false): void
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



    /**
     * @return User[] Returns an array of User objects
     */
    public function findByRole($value): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.roles = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNonSellers(): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        $queryBuilder->select('u')
            ->where($queryBuilder->expr()->not($queryBuilder->expr()->isMemberOf(':sellerRole', 'u.roles')))
            ->setParameter('sellerRole', 'ROLE_SELLER');

        return $queryBuilder->getQuery()->getResult();
    }

    public function findSingleUserRoleUsers(): array
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u')
            ->leftJoin('u.roles', 'r')
            ->groupBy('u')
            ->having('COUNT(r) = 1')
            ->andHaving("MAX(r.name) = 'ROLE_USER'");

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all users who have only one role, and that role is 'ROLE_USER'.
     *
     * @return User[]
     */
    public function findAllWithUserRole(): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.roles', 'r')
            ->groupBy('u.id')
            ->having('COUNT(r.id) = 1')
            ->andWhere('r.name = :role')
            ->setParameter('role', 'ROLE_USER')
            ->getQuery()
            ->getResult();
    }

//    public function findNonSellers(): array
//    {
//        $queryBuilder = $this->createQueryBuilder('u');
//
//        //$queryBuilder = $entityManager->createQueryBuilder();
//        $queryBuilder->select('u')
//            ->from(User::class, 'u')
//            ->where($queryBuilder->expr()->not($queryBuilder->expr()->isMemberOf(':sellerRole', 'u.roles')));
//
//        $queryBuilder->setParameter('sellerRole', 'ROLE_SELLER');
//
//        return $queryBuilder->getQuery()->getResult();
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
