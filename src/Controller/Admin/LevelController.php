<?php

namespace App\Controller\Admin;

use App\Entity\Imports;
use App\Entity\Level;
use App\Form\Admin\LevelType;
use App\Form\Admin\ImportsType;
use App\Repository\LevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LevelController extends AbstractController
{
    /**
     * @Route("/admin/niveaux-d-etudes/index", name="levels_index")
     */
    public function index(LevelRepository $levelRepository): Response
    {
        $levels = $levelRepository->findAll();
        return $this->render('admin/level/index.html.twig', [
            'levels' => $levels
        ]);
    }

    /**
     * @Route("/admin/niveaux-d-etudes/ajouter", name="add_level")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $level = new Level();
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($level);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le niveau <strong>{$level->getWording()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_level');
        }
        return $this->render('admin/level/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/niveau-d-etude/{coded}/mise-a-jour", name="level_edit")
     * @return Response
     */
    public function edit(Level $level, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($level);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le Niveau a été modifié avec succès !"
            );
            return $this->redirectToRoute('levels_index');
        }
        return $this->render('admin/level/edit.html.twig', [
            'form' => $form->createView(),
            'level' => $level
        ]);
    }

    /**
     * @Route("/admin/niveau-d-etude/{id}/supprimer", name="delete_level")
     * @return Response
     */
    public function delete(Level $level, LevelRepository $levelRepo, EntityManagerInterface $entityManager)
    {
        if($levelRepo->findOneBy(['id' => $level])){
            $entityManager->remove($level);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        }else{
            $this->addFlash(
                'warning',
                "Ce niveau n'existe pas dans la base de données !"
            );
        }  
        return $this->redirectToRoute('levels_index');
    }

    /**
     * @Route("/admin/ajouter-des-niveaux-d-etudes-par-importation", name="imports_levels")
     */
    public function import(LevelRepository $levelRepository, 
    Request $request, EntityManagerInterface $entityManager): Response
    {
        $imports = new Imports();
        $form = $this->createForm(ImportsType::class, $imports);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $file = $form->get('file')->getData();
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
                    /* Si c'est la première ligne (en-tête), on passe à la ligne suivante */
                    if ($i == 1) 
                    {
                        continue;
                    }
                    /* Vérifiez si les cellules (coded, wording) de la ligne contiennent des données */
                    $coded = $data[0];
                    $wording = $data[1];
                    if (empty($coded) || empty($wording)) 
                    {
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        if (!$levelRepository->findOneBy(['coded' => $coded, 'wording' => $wording])){
                            $level = new Level();
                            $level->setCoded($coded);
                            $level->setWording($wording);
                            $entityManager->persist($level);
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

                if ($i == 0 || $i == 1) 
                {
                    $this->addFlash(
                        'warning',
                        "L'importation a échouée !</br>Le fichier semble être vide. Vérifiez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_levels');
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
                        "Félicitations, tout les niveaux ont été ajoutés !"
                    );
                    return $this->redirectToRoute('levels_index');
                }

                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) {

                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_levels');
                }

                if ($totalErrorsLines > 0) {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_levels');
                }

            } 
            else 
            {
                /* Erreur de chargement du fichier */
                $this->addFlash(
                    'warning',
                    "Erreur lors de chargmenet du fichier !"
                );
                return $this->redirectToRoute('imports_levels');
            }
        }

        return $this->render('admin/level/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
