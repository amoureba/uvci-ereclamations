<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{
    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

    public function getStats(){
        $users = $this->getUsersCount();
        $students = $this->getStudentsCount();
        $teachers = $this->getTeachersCount();
        $nbClaims = $this->getClaimsCount();
        $semesters = $this->getSemesterCount();

        return compact('users', 'students', 'teachers', 'nbClaims', 'semesters');
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getStudentsCount()
    {
        return $this->manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u WHERE u.profile = 'ETUDIANT'")->getSingleScalarResult();
    }

    public function getTeachersCount()
    {
        return $this->manager->createQuery("SELECT COUNT(u) FROM App\Entity\User u WHERE u.profile = 'ENSEIGNANT'")->getSingleScalarResult();
    }

    public function getClaimsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Claim c')->getSingleScalarResult();
    }

    public function getSemesterCount()
    {
        return $this->manager->createQuery('SELECT COUNT(s) FROM App\Entity\Semester s')->getSingleScalarResult();
    }

}