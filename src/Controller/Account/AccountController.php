<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Entity\Claim;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Entity\Evaluation;
use App\Entity\Examination;
use App\Entity\PasswordUpdate;
use App\Form\UpdateAccountType;
use App\Form\PasswordUpdateType;
use App\Repository\ClaimRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Mise à jour des informations de l'utilisateur en ligne
     * @Route("/compte/mise-a-jour", name="update_user_account")
     * @return Response
     */
    public function update(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateAccountType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /* SI AVATAR */
            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $originalFilname = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilname);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();
                $avatarFile->move($this->getParameter('avatars_directory'), $newFilename);
                $user->setAvatar($newFilename);
            }
            /* FIN SI AVATAR */
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Les Modifications ont été enregistrées avec succès !"
            );

            return $this->redirectToRoute('account');
        }
        return $this->render('account/update/data.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Mise à jour du mot de passe de l'utilisateur en ligne
     * @Route("/compte/mise-a-jour-mot-de-passe", name="account_password")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser(); //user on line
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // 1. Vérifier que le oldPassword du formulaire soit le même que le password de l'user
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $encoder->encodePassword($user, $newPassword);
                $user->setPassword($password);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );
                return $this->redirectToRoute('account_logout');
            }
        }
        return $this->render('account/update/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/accueil", name="account")
     */
    public function index(ClaimRepository $claimRepository): Response
    {
        // 1- étudiant
        if ($this->getUser()->getProfile() == "ETUDIANT") {
            $devClaims = $claimRepository->findBy(['author' => $this->getUser(), 'category' => 'DEVOIR']);
            $examsClaims = $claimRepository->findBy(['author' => $this->getUser(), 'category' => 'EXAMEN']);
            $managClaims = $claimRepository->findBy(['author' => $this->getUser(), 'category' => 'AUTRE']);
            /*$managClaims = $claimRepository->findBy(['author' => $this->getUser(), 'category' => 'GESTION']);
            $techClaims = $claimRepository->findBy(['author' => $this->getUser(), 'category' => 'TECHNIQUE']);*/
            return $this->render('account/index/student.html.twig', [
                'dev_claims' => $devClaims,
                'exams_claims' => $examsClaims,
                'm_claims' => $managClaims,
                /*'tech_claims' => $techClaims,*/
            ]);
        }// 2- enseignant      
        else if ($this->getUser()->getProfile() == "ENSEIGNANT") {
            $matters = $this->getUser()->getMatters();
            $examsClaims = $claimRepository->findBy(['category' => 'EXAMEN']);
            $devClaims = $claimRepository->findBy(['category' => 'DEVOIR']);
            return $this->render('account/index/teacher.html.twig', [
                'matters' => $matters,
                'exams_claims' => $examsClaims,
                'dev_claims' => $devClaims,
            ]);
        } 
        /* 3- technicien
        else if ($this->getUser()->getProfile() == "TECHNICIEN") {
            $techClaims = $claimRepository->findByCategory('TECHNIQUE');
            return $this->render('account/index/technician.html.twig', [
                'tech_claims' => $techClaims
            ]);
        } */
        // 4- gestionnaire
        else if ($this->getUser()->getProfile() == "GESTIONNAIRE") {
            $mClaims = $claimRepository->findByCategory('AUTRE');
            /*$mClaims = $claimRepository->findByCategory('GESTION');*/
            return $this->render('account/index/manager.html.twig', [
                'm_claims' => $mClaims
            ]);
        }else if ($this->getUser()->getProfile() == "ADMINISTRATEUR"){
            return $this->redirectToRoute('admin');
        }

    }

    /**
     * Afficher une réclamation (catégorie : technique ou gestion) et permettre l'ajout de reponse
     * @Route("/compte/reclamation-autre/{id}/afficher", name="account_others_claims_view")
     */
    public function answerOtherClaim(ClaimRepository $claimRepository, 
    Claim $claim, 
    Request $req, 
    EntityManagerInterface $em, 
    MailerInterface $mailer): Response
    {
        $otherClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $answer->setAuthor($this->getUser())
                   ->setClaim($claim);
            $em->persist($answer);
            $em->flush();
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $to = $claim->getAuthor()->getEmail();
            /*$claimContent = $claim->getContent();*/
            $claimWording = $claim->getWording();
            $claimCreatedAt = $claim->getCreatedAt();
            $answerContent = $answer->getContent();
            $subject = '[UVCI e-Reclamation] Nouvelle Réponse à votre réclamation';
            $email = (new TemplatedEmail())
                ->from(new Address($from, $fromName))
                ->to($to)
                ->subject($subject)
                ->htmlTemplate('emails/notif_answer_other_claim.html.twig')
                ->context([
                    'claim_wording' => $claimWording,
                    'claim_created_at' => $claimCreatedAt,
                    'answer_content' => $answerContent,
                ]);
            try 
            {
                $mailer->send($email);
            } 
            catch (TransportExceptionInterface $e) {
            }
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            /*return $this->redirectToRoute('account');*/
            return $this->redirectToRoute('account_others_claims_view', ['id' => $claim->getId()]);
        }
        return $this->render('account/view_claims/others.html.twig', [
            'other_claim' => $otherClaim,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie devoir) et permettre l'ajout de reponse
     * @Route("/compte/devoir/{id}/reclamation/{claim_id}/afficher", name="account_evaluations_claims_view")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function answerDevClaims(ClaimRepository $claimRepository, 
    Evaluation $evaluation, 
    Claim $claim, 
    Request $req,
    MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        $evaluationClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $answer->setAuthor($this->getUser())
                   ->setClaim($claim);
            $em->persist($answer);
            $em->flush();
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $to = $claim->getAuthor()->getEmail();
            /*$claimContent = $claim->getContent();*/
            $claimWording = $claim->getWording();
            $claimCreatedAt = $claim->getCreatedAt();
            $evaluationWording = $evaluation->getWording();
            $evaluationMatter = $evaluation->getMatter()->getWording();
            $evaluationSemester = $evaluation->getSemester()->getSlug();
            $answerContent = $answer->getContent();
            $subject = '[UVCI e-Reclamation] Nouvelle Réponse à votre réclamation';
            $email = (new TemplatedEmail())
                ->from(new Address($from, $fromName))
                ->to($to)
                ->subject($subject)
                ->htmlTemplate('emails/notif_answer_eva_claim.html.twig')
                ->context([
                    'claim_wording' => $claimWording,
                    'claim_created_at' => $claimCreatedAt,
                    'evaluation_wording' => $evaluationWording,
                    'evaluation_matter' => $evaluationMatter,
                    'evaluation_semester' => $evaluationSemester,
                    'answer_content' => $answerContent,
                ]);
            try 
            {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {}
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            /* return $this->redirectToRoute('account'); */
            return $this->redirectToRoute('account_evaluations_claims_view', ['id' => $evaluation->getId(), 'claim_id' => $claim->getId()]);
        }
        return $this->render('account/view_claims/evaluations.html.twig', [
            'evaluation_claim' => $evaluationClaim,
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie examen) et permettre l'ajout de reponse
     * @Route("/compte/examen/{id}/reclamation/{claim_id}/afficher", name="account_examination_claims_view")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function answerExamClaims(ClaimRepository$claimRepository, 
    Claim $claim, 
    Examination $examination, 
    Request $req, 
    MailerInterface $mailer, 
    EntityManagerInterface $em): Response
    {
        $examClaim = $claimRepository->findOneBy(['id' => $claim]);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $answer->setAuthor($this->getUser())
                   ->setClaim($claim);
            $em->persist($answer);
            $em->flush();
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $to = $claim->getAuthor()->getEmail();
            $claimWording = $claim->getWording();
            $claimCreatedAt = $claim->getCreatedAt();
            $examinationMatter = $examination->getMatter()->getWording();
            $examSemester = $examination->getExam()->getSemester()->getSlug();
            $examSession = $examination->getExam()->getSession();
            $answerContent = $answer->getContent();
            $subject = '[UVCI e-Reclamation] Nouvelle Réponse à votre réclamation';
            $email = (new TemplatedEmail())
                ->from(new Address($from, $fromName))
                ->to($to)
                ->subject($subject)
                ->htmlTemplate('emails/notif_answer_exam_claim.html.twig')
                ->context([
                    'claim_wording' => $claimWording,
                    'claim_created_at' => $claimCreatedAt,
                    'examination_matter' => $examinationMatter,
                    'exam_semester' => $examSemester,
                    'exam_session' => $examSession,
                    'answer_content' => $answerContent,
                ]);
            try 
            {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            /*return $this->redirectToRoute('account');*/
            return $this->redirectToRoute('account_examination_claims_view', ['id' => $examination->getId(), 'claim_id' => $claim->getId()]);
        }
        return $this->render('account/view_claims/examinations.html.twig', [
            'exam_claim' => $examClaim,
            'examination' => $examination,
            'form' => $form->createView(),
        ]);
    }

}
