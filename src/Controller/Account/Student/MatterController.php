<?php

namespace App\Controller\Account\Student;

use App\Entity\Matter;
use App\Entity\Semester;
use App\Entity\Registration;
use App\Repository\EvaluationRepository;
use App\Repository\RegistrationRepository;
use App\Repository\MatterSpecialtyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MatterController extends AbstractController
{
    /**
     * Les matières de l'étudiant par rapport à une inscription
     * @Route("/compte/etudiant/inscription/{id}/matieres-et-devoirs", name="students_registration_evaluations_matters_index")
     */
    public function index(Registration $reg, EvaluationRepository $evaluationRepo, MatterSpecialtyRepository $matterSpecialtyRepo, RegistrationRepository $registrationRepo): Response
    {
        //Etudiant
        $registration = $registrationRepo->find($reg);
        $semester = $registration->getSemester();
        $level = $registration->getLevel();
        $specialty = $registration->getSpecialty();
        $mattersSpecialties = $matterSpecialtyRepo->findBy(
            [
                "specialty" => $specialty,
                "level" => $level,
                "semester" => $semester->getWording()
            ]
        );
        $evaluations = $evaluationRepo->findBy(['semester' => $semester]);
        return $this->render('account/student/matter/evaluations_and_matters.html.twig', [
            'mattersSpecialties' => $mattersSpecialties,
            'registration' => $registration,
            'semester' => $semester,
            'level' => $level,
            'specialty' => $specialty,
            'evaluations' => $evaluations,
        ]);
    }

    /**
     * Les devoirs pour une (matière, semestre) à voir (levels+specialty)
     * @Route("/compte/etudiant/matiere/{id}/devoirs/semestre/{semester_slug}", name="show_tasks_matter")
     * @ParamConverter("semester", options={"mapping": {"semester_slug": "slug"}})
     */
    public function show(Matter $matter, Semester $semester, EvaluationRepository $evaluationRepository, RegistrationRepository $registrationRepo): Response
    {
        $evaluations = $evaluationRepository->findBy(['matter' => $matter, 'semester' => $semester, 'type' => 'Devoir']);
        $registration = $registrationRepo->findOneBy(['user' => $this->getUser(), 'semester' => $semester]);
        return $this->render('account/student/evaluation/evaluations.html.twig', [
            'matter' => $matter,
            'semester' => $semester,
            'registration' => $registration,
            'evaluations' => $evaluations,
        ]);
    }

    /**
     * EXAMENS pour une matière
     * @Route("/compte/etudiant/matiere/{id}/examens", name="show_exams_matter")
     */
    public function showExams(Matter $matter, EvaluationRepository $evaluationRepository): Response
    {
        $exams = $evaluationRepository->findBy(['matter' => $matter, 'type' => 'Examen']);
        return $this->render('account/student/exam/exams.html.twig', [
            'matter' => $matter,
            'exams' => $exams
        ]);
    }

}
