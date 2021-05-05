<?php

namespace App\Controller\Admin;

use App\Entity\Claim;
use App\Form\ClaimType;
use App\Entity\Evaluation;
use App\Form\Admin\EvaluationType;
use App\Form\Admin\EvaluationsType;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvaluationController extends AbstractController
{

    /**
     * @Route("/admin/evaluations/index", name="admin_evaluations_index")
     */
    public function index(EvaluationRepository $evaluationRepository): Response
    {
        $tasks = $evaluationRepository->findByType('DEVOIR');
        return $this->render('admin/evaluation/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/admin/evaluations/add", name="admin_add_evaluations")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setType('DEVOIR');
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le devoir a été ajouté avec succès !"
            );
            return $this->redirectToRoute('admin_add_evaluations');
        }
        return $this->render('admin/evaluation/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/evaluations/{id}/delete", name="admin_delete_evaluations")
     * @return Response
     */
    public function delete(Evaluation $evaluation, EvaluationRepository $evaluationRepo, EntityManagerInterface $entityManager)
    {
        if ($evaluationRepo->findOneBy(['id' => $evaluation])) {
            $entityManager->remove($evaluation);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Ce devoir n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('admin_evaluations_index');
    }

    /**
     * @Route("/admin/evaluations/{id}/update", name="admin_edit_evaluations")
     * @return Response
     */
    public function edit(Evaluation $task, Request $request, EntityManagerInterface $entityManager){
        $form = $this->createForm(EvaluationType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été ajoutées avec succès !"
            );
            return $this->redirectToRoute('admin_evaluations_index');
        }
        return $this->render('admin/evaluation/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }
}
