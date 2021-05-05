<?php

namespace App\Controller\Account\Student;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RegistrationRepository;

class StudentRegistrationsController extends AbstractController
{
    /**
     * Afficher les inscriptions de l'étudiant (en ligne)
     * @Route("/compte/etudiant/inscriptions", name="student_semesters_index")
     */
    public function index(RegistrationRepository $registrationRepo): Response
    {
        $registrations = $registrationRepo->findBy(['user' => $this->getUser()]);
        return $this->render('account/student/registration/index.html.twig', [
            'registrations' => $registrations,
        ]);
    }
}
