<?php

namespace App\Controller\Account\Teacher;

use App\Entity\Exam;
use App\Entity\Matter;
use App\Entity\Semester;
use App\Entity\Evaluation;
use App\Entity\Examination;
use App\Repository\ClaimRepository;
use App\Repository\EvaluationRepository;
use App\Repository\ExaminationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class EvaluationController extends AbstractController
{
    /**
     * @Route("/compte/enseignant/ecue", name="teacher_ecue")
     */
    public function ecue(): Response
    {
        $matters = $this->getUser()->getMatters();
        return $this->render('account/teacher/ecue/show_teacher_ecue.html.twig', [
            'matters' => $matters,
        ]);
    }

    /**
     * DEVOIR : Afficher les devoirs d'une matière
     * @Route("/compte/enseignant/matiere/{id}/devoirs", name="teachermatters_schools_tasks_index")
     * @return Response
     */
    public function showEvaluations(Matter $matter, EvaluationRepository $evaluationRepo)
    {
        $schoollsTasks = $evaluationRepo->findBy(['matter' => $matter, 'type' => 'DEVOIR']);
        return $this->render('account/teacher/task/show_matter_evaluations.html.twig', [
            'matter' => $matter,
            'schools_tasks' => $schoollsTasks
        ]);
    }

    /**
     * DEVOIR : Afficher les devoirs d'une matière dans un semestre
     * @Route("/compte/enseignant/matiere/{id}/devoirs/semestre/{semester_slug}", name="teachermatters_schools_tasks_semester")
     * @ParamConverter("semester", options={"mapping": {"semester_slug": "slug"}})
     * @return Response
     */
    public function showSemesterTasks(Matter $matter, Semester $semester, EvaluationRepository $evaluationRepo)
    {
        $schoollsTasks = $evaluationRepo->findBy(
            ['matter' => $matter, 'type' => 'DEVOIR', 'semester' => $semester]
        );
        return $this->render('account/teacher/task/show_semester_tasks.html.twig', [
            'semester' => $semester,
            'matter' => $matter,
            'schools_tasks' => $schoollsTasks
        ]);
    }

    /**
     * DEVOIR : Afficher les reclamations d'un devoir
     * @Route("/compte/enseignant/devoir/{id}/reclamations", name="teachermatters_schools_tasks_claims")
     * @return Response
     */
    public function showSchoolsTasksClaims(Evaluation $evaluation, ClaimRepository $claimRepo)
    {
        $claims = $claimRepo->findBy(['evaluation' => $evaluation]);
        return $this->render('account/teacher/claim/evaluation_claims.html.twig', [
            'evaluation' => $evaluation,
            'claims' => $claims
        ]);
    }

    /**
     * EXAMEN : Afficher les examens d'une matière
     * @Route("/compte/enseignant/matiere/{id}/examens", name="teachermatters_exams_index")
     * @return Response
     */
    public function showExams(Matter $matter, ExaminationRepository $examinationRepo)
    {
        $examinations = $examinationRepo->findBy(
            ['matter' => $matter]
        );
        return $this->render('account/teacher/exam/show_matter_exams.html.twig', [
            'matter' => $matter,
            'examinations' => $examinations
        ]);
    }

    /**
     * EXAMEN: Afficher les reclamations d'un examen
     * @Route("/compte/enseignant/examen/{id}/matiere/{matter_id}/reclamations", name="teachermatters_examination_claims")
     * @ParamConverter("matter", options={"mapping": {"matter_id": "id"}})
     * @return Response
     */
    public function showExaminationClaims(Exam $exam, Matter $matter, ExaminationRepository $examinationRepo, ClaimRepository $claimRepo)
    {
        $examination = $examinationRepo->findOneBy(['exam' => $exam, 'matter' => $matter]);
        $claims = $claimRepo->findBy(['examination' => $examination]);
        return $this->render('account/teacher/claim/examination_claims.html.twig', [
            'examination' => $examination,
            'claims' => $claims
        ]);
    }
    

}
