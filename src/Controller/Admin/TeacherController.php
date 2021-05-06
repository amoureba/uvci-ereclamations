<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Imports;
use App\Form\Admin\UserType;
use App\Form\Admin\ImportsType;
use App\Form\Admin\TeachersRegistrationType;
use App\Form\Admin\TeachersUpdateMattersType;
use App\Repository\UserRepository;
use App\Form\Admin\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\UserResetPasswordType;
use App\Repository\MatterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TeacherController extends AbstractController
{
    /**
     * @Route("/admin/enseignants/index", name="teachers_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $teachers = $userRepository->findby(['profile' => 'ENSEIGNANT']);
        return $this->render('admin/user/teacher/index.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * @Route("/admin/ajouter-des-enseignants", name="add_teachers")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setProfile('ENSEIGNANT');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'enseignant <strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_teachers');
        }
        return $this->render('admin/user/teacher/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression d'enseignants
     * @Route("/admin/supprimer-l-enseignant/{id}", name="delete_teachers")
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $entityManager, UserRepository $userRepo) 
    {
        if ($userRepo->findOneBy(['id' => $user])) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cet enseignant n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('teachers_index');
    }

    /**
     * @Route("/admin/ajouter-des-enseignants-avec-ecue-enseignees", name="advanced_add_teachers")
     * @return Response
     */
    public function advancedAdd(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(TeachersRegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setProfile('ENSEIGNANT');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'enseignant <strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('advanced_add_teachers');
        }

        return $this->render('admin/user/teacher/advanced_add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-de-l-enseignant/{id}", name="update_teacher")
     * @return Response
     */
    public function update(User $user, Request $request, EntityManagerInterface $entityManager,
    UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('blocked')->getData()) {
                $user->setPassword($encoder->encodePassword($user, uniqid()));
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('teachers_index');
        }
        return $this->render('admin/user/teacher/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-des-ecue-de-l-enseignant/{id}", name="update_teacher_matters")
     * @return Response
     */
    public function updateMatters(User $user, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(TeachersUpdateMattersType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les mises à jours ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('teachers_matters', ['id' => $user->getId()]);//159
        }
        return $this->render('admin/user/teacher/update_matters.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/les-ecue-de-l-enseignant/{id}", name="teachers_matters")
     * @return Response
     */
    public function matters(User $user, MatterRepository $matterRepository)
    {
        $matters = $user->getMatters();
        return $this->render('admin/user/teacher/matters.html.twig', [
            'matters' => $matters,
            'user' => $user
        ]);
    }

    /** 
     * @Route("/admin/reinitialisation-du-mot-de-passe-de-l-enseignant/{id}", name="reset_teacher_password")
     * @return Response
     */
    public function resetPassword(User $user, Request $request, EntityManagerInterface$entityManager, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le mot de passe de <strong>{$user->getFullName()}</strong> a été réinitialiser avec succès !"
            );
            return $this->redirectToRoute('teachers_index');
        }

        return $this->render('admin/user/teacher/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * import teachers
     * @Route("/admin/ajouter-des-enseignants-par-importation", name="imports_teachers")
     * @return Response
     */
    public function import(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $imports = new Imports();
        $form = $this->createForm(ImportsType::class, $imports);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
                    /* Si c'est la ligne d'en-tête */
                    if ($i == 1) 
                    {
                        continue;
                    }
                    /* Vérifiez si les cellules (lastName - firstName - email) de la ligne contiennent des données
                       Et si le champs e-mail est valide */
                    $lastName = $data[0];
                    $firstName = $data[1];
                    $email = $data[2];
                    if (empty($lastName) || empty($firstName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
                    {
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        /* Si le (lastName, firstName, email) n'existe pas dans la base de données*/
                        if (!$userRepository->findOneBy(['lastName' => $lastName, 'firstName' => $firstName, 'email' => $email])) 
                        {
                            $user = new User();
                            $user->setLastName($lastName);
                            $user->setFirstName($firstName);
                            $user->setEmail($email);
                            $user->setPassword($encoder->encodePassword($user, 'password'));
                            $user->setProfile('ENSEIGNANT');
                            $user->setBlocked(0);
                            $entityManager->persist($user);
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
                    return $this->redirectToRoute('imports_teachers');
                }

                /* S'il y a des lignes valides, ie (lastName, firstName, email) non vides 
                Et email valide, $entityManager->flush(); */
                if ($okLines > 0) 
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si tout les enseigants ont été importées avec succès */
                if ($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines) 
                {
                    $this->addFlash(
                        'success',
                        "Félicitations, tout les enseignants ont été ajoutées !"
                    );
                    return $this->redirectToRoute('teachers_index');
                }

                /* Si aucuns enseignant n'a pu être importé alors le fichier contient des données */
                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) 
                {
                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_teachers');
                }

                /* S'il y a des lignes qui contiennent des cellules vides */
                if ($totalErrorsLines > 0) 
                {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_teachers');
                }

            }
            else 
            {
                /* Erreur lors du chargement du fichier */
                $this->addFlash(
                    'warning',
                    "Erreur lors du chargmenet du fichier !"
                );
                return $this->redirectToRoute('imports_teachers');
            }
        }

        return $this->render('admin/user/teacher/imports.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
