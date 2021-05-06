<?php

namespace App\Controller\Admin;

use App\Entity\Semester;
use App\Form\Admin\SemesterType;
use App\Repository\SemesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SemesterController extends AbstractController
{

    /**
     * Afficher tout les sémestres
     * @Route("/admin/semestres/index", name="semesters_index")
     */
    public function index(SemesterRepository $semesterRepository): Response
    {
        $semesters = $semesterRepository->findAll();
        return $this->render('admin/semester/index.html.twig', [
            'semesters' => $semesters
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-du-semestre/{slug}", name="semester_edit")
     * @return Response
     */
    public function edit(Semester $semester, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $form = $this->createForm(SemesterType::class, $semester);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $wording = $form->get('wording')->getData();
            $year = $form->get('academicYear')->getData();
            $slug = $slugger->slug($wording . '-' . $year);
            $semester->setSlug($slug);
            $entityManager->persist($semester);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le sémestre a été modifiée avec succès !"
            );
            return $this->redirectToRoute('semesters_index');
        }

        return $this->render('admin/semester/edit.html.twig', [
            'form' => $form->createView(),
            'semester' => $semester,
        ]);
    }

    /**
     * Suppression de sémestres
     * @Route("/admin/supprimer-le-semestre/{slug}", name="delete_semesters")
     * @return Response
     */
    public function delete(Semester $semester, SemesterRepository $semesterRepo, EntityManagerInterface $entityManager)
    {
        if ($semesterRepo->findOneBy(['slug' => $semester])) {
            $entityManager->remove($semester);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Le sémestre que vous avez sélectionné n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('semesters_index');
    }

    /**
     * @Route("/admin/ajouter-des-semestres", name="semesters_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $semester = new Semester();
        $form = $this->createForm(SemesterType::class, $semester);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $wording = $form->get('wording')->getData();
            $year = $form->get('academicYear')->getData();
            $slug = $slugger->slug($wording.'-'.$year);
            $semester->setSlug($slug);
            $entityManager->persist($semester);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le semestre a été ajouté avec succès !"
            );

            return $this->redirectToRoute('semesters_add');
        }
        return $this->render('admin/semester/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
