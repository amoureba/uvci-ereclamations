<?php

namespace App\Repository;

use App\Entity\UploadStudentsFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UploadStudentsFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method UploadStudentsFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method UploadStudentsFile[]    findAll()
 * @method UploadStudentsFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadStudentsFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadStudentsFile::class);
    }

    // /**
    //  * @return UploadStudentsFile[] Returns an array of UploadStudentsFile objects
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
    public function findOneBySomeField($value): ?UploadStudentsFile
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
