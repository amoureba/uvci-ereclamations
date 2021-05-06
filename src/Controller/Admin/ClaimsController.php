<?php

namespace App\Controller\Admin;

use App\Entity\Claim;
use App\Entity\Answer;
use App\Form\AnswerType;
use App\Entity\Evaluation;
use App\Entity\Examination;
use App\Form\ClaimTypeType;
use App\Repository\ClaimRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ClaimsController extends AbstractController
{
    /**
     * Suppression de réclamations depuis le tableau de bord
     * @Route("/admin/reclamation/{id}/supprimer", name="admin_deleteclaims")
     * @return Response
     */
    public function delete(Claim $claim, ClaimRepository $claimRepo, EntityManagerInterface $entityManager)
    {
        if ($claimRepo->findOneBy(['id' => $claim])) {
            $entityManager->remove($claim);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "La suppression a été éffectuée avec succès !"
            );
        } else {
            $this->addFlash(
                'warning',
                "Cette réclamation n'existe pas !"
            );
        }
        return $this->redirectToRoute('admin');
    }

    /**
     * Afficher une réclamation (catégorie : autre) et permettre l'ajout de reponse
     * @Route("admin/reclamation-autre-categorie/{id}", name="admin_others_claims")
     */
    public function other(ClaimRepository$claimRepository, Claim $claim, Request $req, 
    MailerInterface $mailer, EntityManagerInterface $em): Response
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
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $to = $claim->getAuthor()->getEmail();
            /*$claimContent = $claim->getContent();*/
            $claimWording = $claim->getWording();
            $claimCreatedAt = $claim->getCreatedAt();
            $answerContent = $answer->getContent();
            $subject = 'Une nouvelle Réponse !';
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
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('admin_others_claims', ['id' => $claim->getId()]);
        }
        return $this->render('admin/claims/others.html.twig', [
            'other_claim' => $otherClaim,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie devoir) et permettre l'ajout de reponse
     * @Route("admin/devoir/{id}/reclamation/{claim_id}", name="admin_evaluations_claims")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function evaluationClaims(MailerInterface $mailer, ClaimRepository $claimRepository, 
    Evaluation $evaluation, Claim $claim, Request $req, EntityManagerInterface $em): Response
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
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $to = $claim->getAuthor()->getEmail();
            /*$claimContent = $claim->getContent();*/
            $claimWording = $claim->getWording();
            $claimCreatedAt = $claim->getCreatedAt();
            /*$evaluationWording = $claim->getEvaluation()->getWording();
            $evaluationMatter = $claim->getEvaluation()->getMatter()->getWording();
            $evaluationSemester = $claim->getEvaluation()->getSemester()->getSlug();*/
            $evaluationWording = $evaluation->getWording();
            $evaluationMatter = $evaluation->getMatter()->getWording();
            $evaluationSemester = $evaluation->getSemester()->getSlug();
            $answerContent = $answer->getContent();
            $subject = 'Une nouvelle Réponse !';
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
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('admin_evaluations_claims', ['id' => $evaluation->getId(), 'claim_id' => $claim->getId()]);
        }
        return $this->render('admin/claims/evaluations.html.twig', [
            'evaluation_claim' => $evaluationClaim,
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Afficher une réclamation (catégorie examen) et permettre l'ajout de reponse
     * @Route("admin/examen/{id}/reclamation/{claim_id}", name="admin_examination_claims")
     * @ParamConverter("claim", options={"mapping": {"claim_id": "id"}})
     */
    public function exam(ClaimRepository$claimRepository, Claim $claim, Examination $examination, 
    Request $req, EntityManagerInterface $em, MailerInterface $mailer): Response
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
            $subject = 'Une nouvelle Réponse !';
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
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réponse a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('admin_examination_claims', ['id' => $examination->getId(), 'claim_id' => $claim->getId()]);
        }
        return $this->render('admin/claims/examinations.html.twig', [
            'exam_claim' => $examClaim,
            'examination' => $examination,
            'form' => $form->createView(),
        ]);
    }
}
