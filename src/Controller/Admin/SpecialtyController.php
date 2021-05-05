<?php

namespace App\Controller\Admin;

use App\Entity\Imports;
use App\Entity\Specialty;
use App\Form\Admin\ImportsType;
use App\Form\Admin\SpecialtyType;
use App\Repository\SpecialtyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SpecialtyController extends AbstractController
{

    /**
     * specialties
     * @Route("/admin/specialties/index", name="specialties_index")
     */
    public function index(SpecialtyRepository $specialtyRepository): Response
    {
        $specialties = $specialtyRepository->findAll();
        return $this->render('admin/specialty/index.html.twig', [
            'specialties' => $specialties
        ]);
    }

    /**
     * @Route("/admin/specialties//{coded}/update", name="specialty_edit")
     * @return Response
     */
    public function edit(Specialty $specialty, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SpecialtyType::class, $specialty);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($specialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La spécialité a été modifiée avec succès !"
            );
            return $this->redirectToRoute('specialties_index');
        }
        return $this->render('admin/specialty/edit.html.twig', [
            'form' => $form->createView(),
            'specialty' => $specialty,
        ]);
    }

    /**
     * @Route("/admin/specialties/{id}/delete", name="delete_specialty")
     * @return Response
     */
    public function delete(Specialty $specialty, SpecialtyRepository $specialtyRepo, EntityManagerInterface $entityManager)
    {
        if ($specialtyRepo->findOneBy(['id' => $specialty])) {
            $entityManager->remove($specialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cette spécialité n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('specialties_index');
    }

    /**
     * @Route("/admin/specialties/add", name="add_specialty")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $specialty = new Specialty();
        $form = $this->createForm(SpecialtyType::class, $specialty);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($specialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Cette spécialité a été ajouter avec succès !"
            );
            return $this->redirectToRoute('add_specialty');
        }
        return $this->render('admin/specialty/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/specialties/imports", name="imports_specialties")
     */
    public function import(SpecialtyRepository $specialtyRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $imports = new Imports();
        $form = $this->createForm(ImportsType::class, $imports);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if (($handle = fopen($file->getPathname(), "r")) !== false) {
                $i = 0;
                $errorsLines = '';
                $totalErrorsLines = 0;
                $totalLines = 0;
                $okLines = 0;
                $existLines = 0;
                while (($data = fgetcsv($handle, 0, ";")) !== false) 
                {
                    $i++;
                    /* Si c'est la première ligne (en-tête), on passe à la ligne suivante */
                    if ($i == 1) 
                    {
                        continue;
                    }

                    $coded = $data[0];
                    $wording = $data[1];
                    if (empty($coded) || empty($wording)) 
                    {
                        $errorsLines = $errorsLines . ' ' . $i;
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        if (!$specialtyRepository->findOneBy(['coded' => $coded, 'wording' => $wording])) {
                            $specialty = new Specialty();
                            $specialty->setCoded($coded);
                            $specialty->setWording($wording);
                            $entityManager->persist($specialty);
                            $okLines = $okLines + 1;
                        } 
                        else 
                        {
                            $existLines = $existLines + 1;
                        }
                    }
                    $totalLines = $totalLines + 1;
                }
                fclose($handle);

                if ($i == 0 || $i == 1) {
                    $this->addFlash(
                        'warning',
                        "L'importation a échouée !</br>Le fichier semble être vide. Vérifiez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_specialties');
                }

                /* Si le nombres de lignes sans erreurs est supérieur à zéro, flush */
                if ($okLines > 0) 
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si toutes les lignes ont été importées */
                if ($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines) {
                    $this->addFlash(
                        'success',
                        "Félicitations, toutes les spécialités ont été ajoutées !"
                    );
                    return $this->redirectToRoute('specialties_index');
                }

                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) {

                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_specialties');
                }

                if ($totalErrorsLines > 0) {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_specialties');
                }
            } 
            else /* Erreur de chargement du fichier: flash message */
            {
                $this->addFlash(
                    'warning',
                    "Echec importation ! Erreur lors de chargmenet du fichier."
                );
                return $this->redirectToRoute('imports_specialties');
            }
        }

        return $this->render('admin/specialty/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
