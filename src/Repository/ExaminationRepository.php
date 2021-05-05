<?php

namespace App\Repository;

use App\Entity\Examination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Examination|null find($id, $lockMode = null, $lockVersion = null)
 * @method Examination|null findOneBy(array $criteria, array $orderBy = null)
 * @method Examination[]    findAll()
 * @method Examination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExaminationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Examination::class);
    }

    // /**
    //  * @return Examination[] Returns an array of Examination objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Examination
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
