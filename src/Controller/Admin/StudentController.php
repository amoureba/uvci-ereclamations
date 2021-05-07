<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Claim;
use App\Entity\Answer;
use App\Entity\Imports;
use App\Form\AnswerType;
use App\Entity\Evaluation;
use App\Entity\Examination;
use App\Entity\Registration;
use App\Form\Admin\UserType;
use App\Form\Admin\ImportsType;
use App\Form\Admin\OtherClaimType;
use App\Repository\UserRepository;
use App\Repository\ClaimRepository;
use App\Form\Admin\UserRegisterType;
use App\Form\Admin\StudentsDivisionType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\UserResetPasswordType;
use App\Repository\RegistrationRepository;
use App\Form\Admin\UpdateEvaluationClaimType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Admin\UpdateExaminationClaimType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\Admin\StudentsDivisionForImportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class StudentController extends AbstractController
{
    /**
     * @Route("/admin/etudiants/index", name="students_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $students = $userRepository->findby(['profile' => 'ETUDIANT']);
        return $this->render('admin/user/student/index.html.twig', [
            'students' => $students,
        ]);
    }

    /**
     * @Route("/admin/etudiant/{id}/inscriptions", name="show_students_cursus")
     * @return Response
     */
    public function registrations(User $user, RegistrationRepository $registrationRepository)
    {
        $registrations = $registrationRepository->findByUser($user);
        return $this->render('admin/user/student/registrations.html.twig', [
            'registrations' => $registrations,
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/etudiant/supprimer-l-inscription/{id}", name="admindelete_registrations")
     * @return Response
     */
    public function deleteRegistration(RegistrationRepository $registrationRepo, Registration $registration, EntityManagerInterface $entityManager)
    {
        $user = $registration->getUser();
        if ($registrationRepo->findOneBy(['id' => $registration])) {
            $entityManager->remove($registration);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cette ressources n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('show_students_cursus', ['id' => $user->getId()]);
    }

    /**
     * @Route("/admin/etudiants/ajouter", name="add_students")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        //$form = $this->createForm(StudentRegisterType::class, $user);
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $user->setProfile('ETUDIANT');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "L'étudiant <strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_students');
        }
        return $this->render('admin/user/student/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-de-l-etudiant/{id}", name="update_student")
     * @return Response
     */
    public function update(User $user, Request $request, EntityManagerInterface $entityManager, 
    UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            if ($form->get('blocked')->getData())
            {
                $user->setPassword($encoder->encodePassword($user, uniqid()));
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('students_index');
        }
        return $this->render('admin/user/student/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/supprimer-l-etudiant/{id}", name="delete_students")
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
                "L'étudiant que vous avez choisis n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('students_index');
    }

    /**
     * @Route("/admin/reinitialisation-du-mot-de-passe-de-l-etudiant/{id}", name="reset_student_password")
     * @return Response
     */
    public function resetPassword(User $user, Request $request, EntityManagerInterface $entityManager, 
    UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le mot de passe de l'édutiant <strong>{$user->getFullName()}</strong> a été réinitialiser avec succès !"
            );
            return $this->redirectToRoute('students_index');
        }
        return $this->render('admin/user/student/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/etudiants/affectations-individuelles", name="separate_assignment_student")
     * @return Response
     */
    public function separateAssignment(Request $request, UserRepository $userRepository, 
    EntityManagerInterface $entityManager)
    {
        $registration = new Registration();
        $form = $this->createForm(StudentsDivisionType::class, $registration);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $email = $form->get('user')->getData();
            $user = $userRepository->findOneByEmail($email);
            if($user !== null)
            {
                $registration->setUser($user);
                $entityManager->persist($registration);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    "L'affectation a été éffectuée avec succès !"
                );
                return $this->redirectToRoute('students_index');
            }
            else
            {
                $this->addFlash(
                    'warning',
                    "Echèc de l'opération, email non attribué !"
                );
                return $this->redirectToRoute('separate_assignment_student');
            }
        }
        return $this->render('admin/user/student/assignment.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Repartition des étudiants selon le niveau, la specialité, le semestre d'inscription
     * @Route("/admin/etudiants/affectations-groupees-par-importation", name="grouped_assignment_by_import")
     * @return Response
     */
    public function groupedAssignment(Request $request, EntityManagerInterface $entityManager, 
    RegistrationRepository $registrationRepo, UserRepository $userRepository)
    {
        $registration = new Registration();
        $form = $this->createForm(StudentsDivisionForImportType::class, $registration);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $file = $form->get('users')->getData();
            $level = $form->get('level')->getData();
            $specialty = $form->get('specialty')->getData();
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
                    /* Si c'est la première ligne (en-tête), on passe à la ligne suivante */
                    if ($i == 1) 
                    {
                        continue;
                    }

                    $lastName = $data[0];
                    $firstName = $data[1];
                    $email = $data[2];
                    if (empty($lastName) || empty($firstName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
                    {
                        $totalErrorsLines = $totalErrorsLines + 1;
                    } 
                    else 
                    {
                        /* Si l'étudiant existe dans la table des étudiants */
                        if ($student = $userRepository->findOneBy(['lastName' => $lastName, 'firstName' => $firstName, 'email' => $email]))
                        {
                            /* Si l'étudiant, le niveau, la spécialité et le semestre n'existe pas dans la table des inscriptions */
                            if(!$registrationRepo->findOneBy(['user' => $student, 'level' => $level, 'specialty' => $specialty, 'semester' => $semester]))
                            {
                                $registrationStudent = new Registration();
                                $registrationStudent->setUser($student);
                                $registrationStudent->setLevel($level);
                                $registrationStudent->setSpecialty($specialty);
                                $registrationStudent->setSemester($semester);
                                $entityManager->persist($registrationStudent);
                                $okLines = $okLines + 1;
                            }
                            else
                            {
                                $existLines = $existLines + 1;
                            }    
                        }
                        else /* Si l'étudiant n'existe pas dans la table des étudiants, code à revoir... */
                        {
                            /*$newStudent = new User();
                            $newStudent->setLastName($lastName);
                            $newStudent->setFirstName($firstName);
                            $newStudent->setEmail($email);
                            $entityManager->persist($newStudent);
                            $entityManager->flush();*/
                            $totalErrorsLines = $totalErrorsLines + 1;
                            /* faire un new registration ...*/
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
                    return $this->redirectToRoute('grouped_assignment_by_import');
                }

                /* S'il y a des lignes qui contiennent des cellules valides */
                if ($okLines > 0) 
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si toutes les lignes ont été importées avec succès */
                if ($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines) 
                {
                    $this->addFlash(
                        'success',
                        "Félicitations, l'importation a été éffectuée avec succès !"
                    );
                    return $this->redirectToRoute('students_index');
                }

                /* Si aucune ligne n'a pu être importé alors que le fichier contient des données */
                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) {
                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('grouped_assignment_by_import');
                }

                /* S'il y a des lignes qui contiennent des cellules vides */
                if ($totalErrorsLines > 0) {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('grouped_assignment_by_import');
                }
            } 
            else 
            {
                /* Erreur lors du chargement du fichier */
                $this->addFlash(
                    'warning',
                    "Erreur lors du chargmenet du fichier !"
                );
                return $this->redirectToRoute('grouped_assignment_by_import');
            }
        }

        return $this->render('admin/user/student/assignments.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Ajouter des étudiants par importation
     * @Route("/admin/etudiants/ajouter-par-importation", name="imports_students")
     * @return Response
     */
    public function importStudents(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, 
    UserPasswordEncoderInterface $encoder)
    {
        /*$uploadStudents = new UploadStudents();
        $form = $this->createForm(UploadStudentsType::class, $uploadStudents);*/
        $import = new Imports();
        $form = $this->createForm(ImportsType::class, $import);
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
                while(($data = fgetcsv($handle, 0, ";")) !== false)
                {
                    $i++;
                    /* Si c'est la ligne d'en-tête */
                    if($i == 1)
                    {
                        continue;
                    }
                    /* Vérifiez si les cellules (lastName - firstName - email) de la ligne contiennent des données
                       Et si le champs e-mail est valide */
                    $lastName = $data[0];
                    $firstName = $data[1];
                    $email = $data[2];
                    if(empty($lastName) || empty($firstName) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $totalErrorsLines = $totalErrorsLines + 1;
                    }
                    else
                    {
                        /* Si le (lastName, firstName, email) n'existe pas dans la base de données*/
                        if(!$userRepository->findOneBy(['lastName' => $lastName, 'firstName' => $firstName, 'email' => $email]))
                        {
                            $user = new User();
                            $user->setLastName($lastName);
                            $user->setFirstName($firstName);
                            $user->setEmail($email);
                            $user->setPassword($encoder->encodePassword($user, 'password'));
                            $user->setProfile('ETUDIANT');
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

                if ($i == 0 || $i == 1) 
                {
                    $this->addFlash(
                        'warning',
                        "L'importation a échouée !</br>Le fichier semble être vide. Vérifiez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_students');
                }

                /* S'il y a des lignes valides, ie (lastName, firstName, email) non vides 
                Et email valide, $entityManager->flush(); */
                if( $okLines > 0 )
                {
                    $entityManager->flush();
                }

                $succes = $okLines + $existLines;
                /* Si tout les étudiants ont été importées avec succès */
                if ($okLines == $totalLines || $existLines == $totalLines || $succes == $totalLines) {
                    $this->addFlash(
                        'success',
                        "Félicitations, tout les étudiants ont été ajoutées !"
                    );
                    return $this->redirectToRoute('students_index');
                }

                if ($okLines == 0 and $totalErrorsLines == $totalLines and $totalLines > 0) {
                    $this->addFlash(
                        'danger',
                        "L'importation a échouée !</br>Vérifiez que les lignes du fichier sont tous correctes et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_students');
                }

                if ($totalErrorsLines > 0) {
                    $this->addFlash(
                        'warning',
                        "Les lignes contenant des cellules vides n'ont pu être importer. Vérifez et rééssayez !"
                    );
                    return $this->redirectToRoute('imports_students');
                }
            }
            else
            {
                // Erreur lors du chargement du fichier */
                $this->addFlash(
                    'warning',
                    "Erreur lors de chargmenet du fichier, échec importation !"
                );
                return $this->redirectToRoute('imports_students');
            }

        }

        return $this->render('admin/user/student/imports.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/etudiant/{id}/les-reclamations", name="show_students_claims")
     * @return Response
     */
    public function claims(User $user, ClaimRepository $claimRepository)
    {
        $claims = $claimRepository->findByAuthor($user);
        $exams = $claimRepository->findBy(['author' => $user, 'category' => 'EXAMEN']);
        $evaluations = $claimRepository->findBy(['author' => $user, 'category' => 'DEVOIR']);
        $others = $claimRepository->findBy(['author' => $user, 'category' => 'AUTRE']);
        /*$technics = $claimRepository->findBy(['author' => $user, 'category' => 'TECHNIQUE']);*/
        return $this->render('admin/user/student/claims.html.twig', [
            'claims' => $claims,
            'exams' => $exams,
            'evaluations' => $evaluations,
            'others' => $others,
            /*'technics' => $technics,*/
            'user' => $user
        ]);
    }

    /**
     * Afficher une réclamation (catégorie examen) et permettre l'ajout de reponse
     * @Route("admin/etudiants/examen/{id}/reclamation/{claim_id}", name="admin_view_students_examination_claims")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function ViewExaminationClaims(ClaimRepository $claimRepository, Claim $claim, Examination $examination, Request $req, EntityManagerInterface $em): Response 
    {
        $examClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setAuthor($this->getUser())
                ->setClaim($claim);

            $em->persist($answer);
            $em->flush();
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('admin_view_students_examination_claims', ['id' => $examination->getId(), 'claim_id' => $claim->getId()]);
        }
        return $this->render('admin/user/student/details_claims/details_examination_claims.html.twig', [
            'exam_claim' => $examClaim,
            'examination' => $examination,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie devoir) et permettre l'ajout de reponse
     * @Route("admin/etudiants/devoir/{id}/reclamation/{claim_id}", name="students_evaluations_claims")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function viewEvaluationClaims(ClaimRepository $claimRepository, Evaluation $evaluation, Claim $claim, Request $req, EntityManagerInterface $em): Response 
    {
        $evaluationClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setAuthor($this->getUser())
                   ->setClaim($claim);
            $em->persist($answer);
            $em->flush();
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('students_evaluations_claims', ['id' => $evaluation->getId(), 'claim_id' => $claim->getId()]);
        }

        return $this->render('admin/user/student/details_claims/details_evaluation_claims.html.twig', [
            'evaluation_claim' => $evaluationClaim,
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie : autre) et permettre l'ajout de reponse
     * @Route("admin/etudiant/reclamation-autre/{id}", name="admin_students_others_claims")
     */
    public function otherClaims(ClaimRepository $claimRepository, Claim $claim, Request $req, EntityManagerInterface $em): Response 
    {
        $otherClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setAuthor($this->getUser())
                ->setClaim($claim);

            $em->persist($answer);
            $em->flush();
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('admin_students_others_claims', ['id' => $claim->getId()]);
        }

        return $this->render('admin/user/student/details_claims/others.html.twig', [
            'other_claim' => $otherClaim,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/etudiants/supprimer-la-reclamation/{id}", name="admin_delete_students_claims")
     * @return Response
     */
    public function deleteClaim(Claim $claim, EntityManagerInterface $entityManager, ClaimRepository $claimRepo) 
    {
        $auth = $claimRepo->findOneBy(['id' => $claim])->getAuthor();
        if($claimRepo->findOneBy(['id' => $claim]))
        {
            $entityManager->remove($claim);
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
                "La ressource sélectionnée n'existe pas dans la base de données !"
            );
            return $this->redirectToRoute('students_index');
        }
        return $this->redirectToRoute('show_students_claims', ['id' => $auth->getId()]);
    }

    /**
     * @Route("/admin/etudiants/mise-a-jour-reclamation-sur-devoir/{id}", name="admin_update_students_evaluations_claims")
     * @return Response
     */
    public function showEvaluationClaim(Claim $claim, Request $request, EntityManagerInterface $entityManager) 
    {
        $form = $this->createForm(UpdateEvaluationClaimType::class, $claim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($claim);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('show_students_claims', ['id' => $claim->getAuthor()->getId()]);
        }
        return $this->render('admin/user/student/details_claims/update_evaluation_claim.html.twig', [
            'form' => $form->createView(),
            'claim' => $claim
        ]);      
    }

    /**
     * @Route("/admin/etudiants/mise-a-jour-reclamation-autre/{id}", name="admin_update_students_others_claims")
     * @return Response
     */
    public function showOtherClaim(Claim $claim, Request $request, EntityManagerInterface $entityManager) 
    {
        $form = $this->createForm(OtherClaimType::class, $claim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($claim);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('show_students_claims', ['id' => $claim->getAuthor()->getId()]);
        }
        return $this->render('admin/user/student/details_claims/update_other_claim.html.twig', [
            'form' => $form->createView(),
            'claim' => $claim
        ]);
    }

    /**
     * @Route("/admin/etudiants/mise-a-jour-reclamation-sur-examen/{id}", name="admin_update_students_examination_claims")
     * @return Response
     */
    public function showExaminationClaim(Claim $claim, Request $request, EntityManagerInterface $entityManager) 
    {
        $form = $this->createForm(UpdateExaminationClaimType::class, $claim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($claim);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les modifications ont été enregistrées avec succès !"
            );
            return $this->redirectToRoute('show_students_claims', ['id' => $claim->getAuthor()->getId()]);
        }
        return $this->render('admin/user/student/details_claims/update_examination_claim.html.twig', [
            'form' => $form->createView(),
            'claim' => $claim
        ]);
    }


}
