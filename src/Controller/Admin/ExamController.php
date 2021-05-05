<?php

namespace App\Controller\Admin;

use App\Entity\Exam;
use App\Entity\Examination;
use App\Form\Admin\ExamType;
use App\Form\Admin\ExamUpdateType;
use App\Repository\ExamRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ExaminationRepository;
use App\Repository\MatterSpecialtyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExamController extends AbstractController
{
    /**
     * Afficher toutes les sessions
     * @Route("/admin/exams/index", name="exams_index")
     */
    public function index(ExamRepository $examRepository): Response
    {
        $exams = $examRepository->findAll();
        return $this->render('admin/exam/index.html.twig', [
            'exams' => $exams,
        ]);
    }

    /**
     * Ajouter une session pour (niveau, spécialité, semestre)
     * @Route("/admin/exams/add", name="add_exams")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, MatterSpecialtyRepository $matterSpecialtyRepository, SluggerInterface $slugger): Response
    {
        $exam = new Exam();
        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $specialty = $form->get('specialty')->getData();
            $level = $form->get('level')->getData();
            $semester = $form->get('semester')->getData();
            $session = $form->get('session')->getData();
            $slugExam = $slugger->slug($session . '-' . $semester->getSlug(). '-'.$level->getWording(). '-'.$specialty->getWording());
            $exam->setSlug($slugExam);
            $exam->setArchived(0);
            $entityManager->persist($exam);//persist exam
            //examinations
            $mattersSpecialties = $matterSpecialtyRepository->findBy(
                [
                    "level" => $level,
                    "specialty" => $specialty,
                    "semester" => $semester->getWording()
                ]
            );
            foreach($mattersSpecialties as $matterSpecialty){
                $examination = new Examination();
                $examination->setMatter($matterSpecialty->getMatter());
                $examination->setExam($exam);
                $examination->setSlug($slugger->slug($slugExam. '-'. $matterSpecialty->getMatter()->getWording()));
                $entityManager->persist($examination);//persist examination
            }
            $entityManager->flush();//flush all
            $this->addFlash(
                'success',
                "L'examen a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_exams');
        }
        return $this->render('admin/exam/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Archiver un examen
     * @Route("/admin/exams/{id}/update", name="update_exams")
     * @return Response
     */
    public function update(Request $request, 
    EntityManagerInterface $entityManager, 
    Exam $exam, ExamRepository $examRepo): Response
    {
        if ($examRepo->findOneBy(['id' => $exam])) {
            if($exam->getArchived()){
                $exam->setArchived(false);
                $entityManager->persist($exam);
                $entityManager->flush();
            }else{
                $exam->setArchived(true);
                $entityManager->persist($exam);
                $entityManager->flush();
            }
            $this->addFlash(
                'success',
                "L'opération a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cet examen n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('exams_index');
        /*
        $form = $this->createForm(ExamUpdateType::class, $exam);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('archived')->getData()){
                $exam->setArchived(true);
                $entityManager->persist($exam);
                $entityManager->flush();
            }else{
                $exam->setArchived(false);
                $entityManager->persist($exam);
                $entityManager->flush();
            }
            $this->addFlash(
                'success',
                "L'examen a été modifié avec succès !"
            );
            return $this->redirectToRoute('exams_index');
        }
        return $this->render('admin/exam/update.html.twig', [
            'form' => $form->createView()
        ]);
        */
    }

    /**
     * Suppression d'un examen par son id
     * @Route("/admin/exams/{id}/delete", name="delete_exam")
     */
    public function delete(ExamRepository $examRepo, Exam $exam, EntityManagerInterface $entityManager): Response
    {
        if($examRepo->findOneBy(['id' => $exam])){
            $entityManager->remove($exam);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cet examen n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('exams_index');
    }

    /**
     * Afficher les détails d'un examen
     * @Route("/admin/exams/{slug}/details", name="details_exam")
     */
    public function details(ExaminationRepository $examinationRepo, Exam $exam): Response
    {
        $examinations = $examinationRepo->findByExam($exam);
        return $this->render('admin/exam/details.html.twig', [
            'exam' => $exam,
            'examinations' => $examinations,
        ]);
    }

    /**
     * Suppression d'une ligne de composition par son id
     * @Route("/admin/exams/examinations/{id}/delete", name="delete_exams_examinations")
     */
    public function deleteExamination(ExaminationRepository $examinationRepo, Examination $examination, EntityManagerInterface $entityManager): Response
    {
        if ($examinationRepo->findOneBy(['id' => $examination])) {
            $entityManager->remove($examination);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "La ressource demandée n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('details_exam');
    }
}
