<?php

namespace App\Controller\Admin;

use App\Entity\Matter;
use App\Entity\Imports;
use App\Form\Admin\MatterType;
use App\Form\Admin\ImportsType;
use App\Repository\MatterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MatterController extends AbstractController
{

    /**
     * @Route("/admin/matters/index", name="matters_index")
     */
    public function index(MatterRepository $matterRepository): Response
    {
        $matters = $matterRepository->findAll();
        return $this->render('admin/matter/index.html.twig', [
            'matters' => $matters
        ]);
    }

    /**
     * @Route("/admin/matters/{coded}/update", name="matter_edit")
     * @return Response
     */
    public function edit(Matter $matter, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(MatterType::class, $matter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matter);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées !"
            );
            return $this->redirectToRoute('matters_index');
        }
        return $this->render('admin/matter/edit.html.twig', [
            'form' => $form->createView(),
            'matter' => $matter,
        ]);
    }

    /**
     * @Route("/admin/matters/{id}/delete", name="delete_matter")
     * @return Response
     */
    public function delete(Matter $matter, MatterRepository $matterRepo, EntityManagerInterface $entityManager)
    {
        if($matterRepo->findOneBy(['id' => $matter])){
            $entityManager->remove($matter);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        }
        else
        {
            $this->addFlash(
                'warning',
                "Cette matière n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('matters_index');
    }

    /**
     * @Route("/admin/matters/add", name="add_matter")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $matter = new Matter();
        $form = $this->createForm(MatterType::class, $matter);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($matter);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La matière <strong>{$matter->getWording()}</strong> a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('add_matter');
        }

        return $this->render('admin/matter/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/matters/imports", name="imports_matters")
     */
    public function import(MatterRepository $matterRepository, Request $request, EntityManagerInterface $entityManager): Response
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
                    /* Vérifiez si les cellules (coded, wording) de la ligne contiennent des données */
                    if (empty($coded) || empty($wording)) 
                    {
                        $errorsLines = $errorsLines .' '. $i;
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        /* Vérifier si le (coded, wording) n'existe pas déjà dans la base de données */
                        if (!$matterRepository->findOneBy(['coded' => $coded, 'wording' => $wording])) {
                            $matter = new Matter();
                            $matter->setCoded($coded);
                            $matter->setWording($wording);
                            $entityManager->persist($matter);
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
                    return $this->redirectToRoute('imports_matters');
                }

                /* Si le nombres de lignes sans erreurs est supérieur à zéro, flush */
                if ($okLines > 0) 
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si toutes les lignes ont été importées */
                if($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines)
                {
                    $this->addFlash(
                        'success',
                        "Félicitations, toutes les matières ont été ajoutées !"
                    );
                    return $this->redirectToRoute('matters_index');
                }

                if($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0)
                {

                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_matters');
                }

                if($totalErrorsLines > 0 )
                {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_matters');
                }

            } 
            else /* Erreur de chargement du fichier: flash message */
            {
                $this->addFlash(
                    'warning',
                    "Echec importation ! Erreur lors de chargmenet du fichier."
                );
                return $this->redirectToRoute('imports_matters');
            }
        }

        return $this->render('admin/matter/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
