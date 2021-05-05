<?php

namespace App\Controller\Account\Teacher;

use App\Entity\Claim;
use App\Entity\Answer;
use App\Form\AnswerType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class TClaimController extends AbstractController
{
    /**
     * Afficher une réclamation sur un devoir puis ajouter une reponse
     * @Route("/compte/enseignant/afficher-la-reclamation-sur-devoir/{id}", name="teachermatters_evaluation_claim_answer")
     */
    public function AddAnswer(Claim $claim, 
    Request $req, 
    EntityManagerInterface $em, MailerInterface $mailer): Response
    {
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
            $evaluationWording = $claim->getEvaluation()->getWording();
            $evaluationMatter = $claim->getEvaluation()->getMatter()->getWording();
            $evaluationSemester = $claim->getEvaluation()->getSemester()->getSlug();
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
            return $this->redirectToRoute('teachermatters_schools_tasks_claims', ['id' => $claim->getEvaluation()->getId()]);
        }

        return $this->render('account/teacher/claim/evaluation_claim_answer.html.twig', [
            'claim' => $claim,
            'form' => $form->createView()
        ]);
    }

    /**
     * Afficher une réclamation sur une examination puis ajouter une reponse
     * @Route("/compte/enseignant/afficher-la-reclamation-sur-examen/{id}", name="teacher_exam_examination_claim_answer")
     */
    public function AddExAnswer(Claim $claim, Request $req, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
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
            $examinationMatter = $claim->getExamination()->getMatter()->getWording();
            $examSemester = $claim->getExamination()->getExam()->getSemester()->getSlug();
            $examSession = $claim->getExamination()->getExam()->getSession();
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
            return $this->redirectToRoute('teachermatters_examination_claims', ['id' => $claim->getExamination()->getExam()->getId(), 'examination_id' => $claim->getExamination()->getId()]);
        }
        return $this->render('account/teacher/claim/examination_claim_answer.html.twig', [
            'claim' => $claim,
            'form' => $form->createView()
        ]);
    }

}
