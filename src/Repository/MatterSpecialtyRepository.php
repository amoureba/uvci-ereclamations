<?php

namespace App\Repository;

use App\Entity\MatterSpecialty;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MatterSpecialty|null find($id, $lockMode = null, $lockVersion = null)
 * @method MatterSpecialty|null findOneBy(array $criteria, array $orderBy = null)
 * @method MatterSpecialty[]    findAll()
 * @method MatterSpecialty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatterSpecialtyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatterSpecialty::class);
    }

    // /**
    //  * @return MatterSpecialty[] Returns an array of MatterSpecialty objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MatterSpecilaty
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
