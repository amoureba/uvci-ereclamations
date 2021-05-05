<?php

namespace App\Repository;

use App\Entity\UserMatter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMatter|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMatter|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMatter[]    findAll()
 * @method UserMatter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMatterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMatter::class);
    }

    // /**
    //  * @return UserMatter[] Returns an array of UserMatter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserMatter
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
