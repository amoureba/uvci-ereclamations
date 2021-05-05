<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Evaluation;
use App\Entity\Registration;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    /*public function findEvaluation($user){

        return $this->createQueryBuilder('e')
                    ->join('e.semester', 's')
                    ->join('s.registrations', 'r')
                    ->join('r.user', 'u')
                    ->select('e as Evaluation')
                    ->where('r.user = :user')
                    ->setParameter('user', $user)
                    ->getQuery()
                    ->getResult();

    }*/

    // /**
    //  * @return Evaluation[] Returns an array of Evaluation objects
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
    public function findOneBySomeField($value): ?Evaluation
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
