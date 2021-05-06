<?php

namespace App\Controller\Admin;

use App\Entity\AcademicYear;
use App\Form\Admin\AcademicYearType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AcademicYearRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AcademicYearController extends AbstractController
{
    /**
     * Afficher toutes les rentrées academiques
     * @Route("/admin/rentrees-academiques/index", name="academics_years_index")
     */
    public function index(AcademicYearRepository $academicYearRepository): Response
    {
        $acamedicsYears = $academicYearRepository->findAll();
        return $this->render('admin/academic_year/index.html.twig', [
            'academics_years' => $acamedicsYears
        ]);
    }

    /**
     * @Route("/admin/rentrees-academiques/ajouter", name="academics_years_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $year = new AcademicYear();
        $form = $this->createForm(AcademicYearType::class, $year);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($year);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La rentée <strong>{$year->getWording()}</strong> a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('academics_years_add');
        }
        return $this->render('admin/academic_year/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifier une rentrée academique
     * @Route("/admin/rentree-academique/{coded}/mise-a-jour", name="academic_year_edit")
     * @return Response
     */
    public function edit(AcademicYear $academicYear, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AcademicYearType::class, $academicYear);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($academicYear);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été ajoutée avec succès !"
            );
            return $this->redirectToRoute('academics_years_index');
        }
        return $this->render('admin/academic_year/edit.html.twig', [
            'form' => $form->createView(),
            'academic_year' => $academicYear
        ]);
    }

    /**
     * @Route("/admin/rentree-academique/{id}/supprimer", name="delete_academic_year")
     * @return Response
     */
    public function delete(AcademicYear $academicYear, AcademicYearRepository $academicYearRepo, EntityManagerInterface $entityManager)
    {
        if ($academicYearRepo->findOneBy(['id' => $academicYear])) {
            $entityManager->remove($academicYear);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "La rentrée que vous avez sélectionnée n'existe pas !"
            );
        }
        return $this->redirectToRoute('academics_years_index');
    }

}
