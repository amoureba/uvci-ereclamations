<?php

namespace App\Controller\Admin;

use App\Entity\Level;
use App\Entity\Matter;
use App\Entity\Specialty;
use App\Entity\MatterSpecialty;
use App\Repository\MatterRepository;
use App\Form\Admin\MatterSpecialtyType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\MatterSpecialtyImportType;
use App\Repository\LevelRepository;
use App\Repository\MatterSpecialtyRepository;
use App\Repository\SpecialtyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MatterSpecialtyController extends AbstractController
{
    /**
     * @Route("/admin/levels-specialties-matters/index", name="matters_levels_specialties_index")
     */
    public function index(MatterSpecialtyRepository $matterSpecialtyRepository): Response
    {
        $mattersSpecialties = $matterSpecialtyRepository->findAll();
        return $this->render('admin/matter_specialty/index.html.twig', [
            'mattersSpecialties' => $mattersSpecialties
        ]);
    }

    /**
     * @Route("/admin/edit-bond/level/{id}/specilaties/{specialty_id}/matters/{matter_id}", name="edit_matter_level_specialty")
     * @ParamConverter("specialty", options={"mapping": {"specialty_id": "id"}})
     * @ParamConverter("matter", options={"mapping": {"matter_id": "id"}})
     */
    public function edit(Matter $matter, 
    Level $level, 
    Specialty $specialty, 
    MatterSpecialtyRepository $matterSpecialtyRepo, 
    Request $request, 
    EntityManagerInterface $entityManager): Response
    {
        $matterSpecialty = $matterSpecialtyRepo->findOneBy([
            'level' => $level,
            'specialty' => $specialty,
            'matter' => $matter
        ]);
        $form = $this->createForm(MatterSpecialtyType::class, $matterSpecialty);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matterSpecialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La liaison a été modifiée avec succès !"
            );
            return $this->redirectToRoute('matters_levels_specialties_index');
        }
        return $this->render('admin/matter_specialty/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/delete-bond/level/{id}/specilaties/{specialty_id}/matters/{matter_id}", name="delete_bond_matter_level_specialty")
     * @ParamConverter("specialty", options={"mapping": {"specialty_id": "id"}})
     * @ParamConverter("matter", options={"mapping": {"matter_id": "id"}})
     */
    public function delete(Matter $matter, 
    Level $level, 
    Specialty $specialty, 
    MatterSpecialtyRepository $matterSpecialtyRepo, 
    Request $request, 
    EntityManagerInterface $entityManager): Response
    {
        if($matterSpecialty = $matterSpecialtyRepo->findOneBy([
            'level' => $level,
            'specialty' => $specialty,
            'matter' => $matter
        ])){
            $entityManager->persist($matterSpecialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La liaison a été supprimée avec succès !"
            );  
        }else{
            $this->addFlash(
                'warning',
                "Ce liaison n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('matters_levels_specialties_index');     
    }

    /**
     * @Route("/admin/levels-specialties-matters/add", name="add_matter_level_specialty")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matterSpecialty = new MatterSpecialty();
        $form = $this->createForm(MatterSpecialtyType::class, $matterSpecialty);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matterSpecialty);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La liaison a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('add_matter_level_specialty');
        }
        return $this->render('admin/matter_specialty/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/levels-specialties-matters/import", name="matter_level_specialty_import")
     * @return Response
     */
    public function repartition(Request $request, 
    EntityManagerInterface $entityManager, 
    MatterRepository $matterRepository,
    MatterSpecialtyRepository $matterSpecialtyRepo)
    {
        $matterSpecialty = new MatterSpecialty();
        $form = $this->createForm(MatterSpecialtyImportType::class, $matterSpecialty);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $file = $form->get('matters')->getData();
            $specialty = $form->get('specialty')->getData();
            $level = $form->get('level')->getData();
            $semester = $form->get('semester')->getData();
            if (($handle = fopen($file->getPathname(), "r")) !== false) 
            {
                $i = 0;
                $totalLines = 0;
                $okLines = 0;
                $totalErrorsLines = 0;
                $existLines = 0;
                while (($data = fgetcsv($handle, 0, ";")) !== false) 
                {
                    $i++;
                    /* Si c'est la ligne d'en-tête */
                    if ($i == 1) {
                        continue;
                    }
                    /* Vérifiez si les champs (coded, wording) de la ligne contiennent des données */
                    $coded = $data[0];
                    $wording = $data[1];
                    if (empty($coded) || empty($wording)) 
                    {
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        /* Si la matière (coded, wording) existe dans la base de données */
                        if ($matter = $matterRepository->findOneBy(['coded' => $coded, 'wording' => $wording]))
                        {
                            /* Si la liaison n'existe pas dans la base de données */
                            if(!$matterSpecialtyRepo->findOneBy(['matter' => $matter, 'specialty' => $specialty, 'level' => $level]))
                            {
                                $matterSpecialtyLevel = new MatterSpecialty();
                                $matterSpecialtyLevel->setMatter($matter);
                                $matterSpecialtyLevel->setSpecialty($specialty);
                                $matterSpecialtyLevel->setLevel($level);
                                $matterSpecialtyLevel->setSemester($semester);
                                $entityManager->persist($matterSpecialtyLevel);
                                $okLines = $okLines + 1;
                            }
                            else
                            {
                                $existLines = $existLines + 1;
                            } 
                        }
                        else
                        {
                            $totalErrorsLines = $totalErrorsLines + 1;
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
                    return $this->redirectToRoute('matter_level_specialty_import');
                }

                /* S'il y a des liaisons valides ont été trouvées. $entityManager->flush(); */
                if ($okLines > 0) 
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si toutes les lignes ont été importées avec succès */
                if ($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines) {
                    $this->addFlash(
                        'success',
                        "Félicitations, tout les laisons ont été ajoutées !"
                    );
                    return $this->redirectToRoute('matters_levels_specialties_index');
                }

                /* Si aucune ligne n'a pu être importé alors le fichier contient des données */
                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) {
                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('matter_level_specialty_import');
                }

                /* S'il y a des lignes qui contiennent des cellules vides */
                if ($totalErrorsLines > 0) {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides<br>ou des données inexistantes dans la base données n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('matter_level_specialty_import');
                }

            } 
            else 
            {
                /* Erreur lors du chargement du fichier */
                $this->addFlash(
                    'warning',
                    "Erreur lors de chargmenet du fichier, échec importation !"
                );
            }
        }

        return $this->render('admin/matter_specialty/import.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
