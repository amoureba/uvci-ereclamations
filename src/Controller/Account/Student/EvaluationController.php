<?php

namespace App\Controller\Account\Student;

use App\Entity\Claim;
use App\Form\ClaimType;
use App\Entity\Evaluation;
use App\Repository\UserRepository;
use App\Repository\ClaimRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RegistrationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EvaluationController extends AbstractController
{
    /**
     * Afficher un devoir par son id et permettre d'ajouter une reclamation
     * @Route("/compte/etudiant/devoir/{id}/ajouter-reclamations", name="show_evaluation_and_do_claims")
     * @return Response
     */
    public function show(Evaluation $evaluation, 
    ClaimRepository $claimRepo, 
    RegistrationRepository $regRepo, 
    Request $req, 
    EntityManagerInterface $em, 
    SluggerInterface $slugger, 
    MailerInterface $mailer): Response
    {
        $registration = $regRepo->findOneBy(['user' => $this->getUser(), 'semester' => $evaluation->getSemester()]);
        $claims = $claimRepo->findBy(
            ['evaluation' => $evaluation, 'author' => $this->getUser()],
            ['createdAt' => 'DESC']
        );
        $claim = new Claim();
        $form = $this->createForm(ClaimType::class, $claim);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        {
            $captureFile = $form['capture']->getData();
            $claimWording = $form['wording']->getData();
            $claimContent = $form['content']->getData();
            /* SI CAPTURE */
            if ($captureFile) 
            {
                $originalFilname = pathinfo($captureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilname);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $captureFile->guessExtension();
                $captureFile->move($this->getParameter('captures_directory'), $newFilename);
                $claim->setCapture($newFilename);
            }
            /* FIN SI CAPTURE */
            $claim->setAuthor($this->getUser())
                  ->setEvaluation($evaluation)
                  ->setCategory($evaluation->getType())
                  ->setArchived(0);
            $em->persist($claim);
            $em->flush();
            
            /* DEBUT ENVOI MAIL */
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $teachers = $evaluation->getMatter()->getUsers();
            $subject = '[UVCI e-Reclamation] Nouvelle Réclamation';
            $evaluationMatter = $evaluation->getMatter()->getWording();
            $evaluationWording = $evaluation->getWording();
            $evaluationDescription = $evaluation->getDescription();
            $evaluationSemester = $evaluation->getSemester()->getSlug();
            foreach($teachers as $teacher)
            {
                $email = (new TemplatedEmail())
                    ->from(new Address($from, $fromName))
                    ->to($teacher->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('emails/notif_eva_claim.html.twig')
                    ->context([
                        'evaluation_matter' => $evaluationMatter,
                        'evaluation_wording' => $evaluationWording,
                        'evaluation_description' => $evaluationDescription,
                        'evaluation_semester' => $evaluationSemester,
                        'claim_wording' => $claimWording,
                        'claim_content' => $claimContent,
                    ]);
                try 
                {
                    $mailer->send($email);
                } 
                catch (TransportExceptionInterface $e) {}
            }
            /*
            $admins = $userRepo->findby(['profile' => 'ADMINISTRATEUR']);
            foreach ($admins as $admin) {
                $email = (new TemplatedEmail())
                    ->from(new Address($from, $fromName))
                    ->to($admin->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('emails/notif_eva_claim.html.twig')
                    ->context([
                        'evaluation_matter' => $evaluationMatter,
                        'evaluation_wording' => $evaluationWording,
                        'evaluation_description' => $evaluationDescription,
                    'evaluation_semester' => $evaluationsemester,
                        'claim_wording' => $claimWording,
                        'claim_content' => $claimContent,
                    ]);
                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {}
            }
            */
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réclamation a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('show_evaluation_and_do_claims', ['id' => $evaluation->getId()]);
        }

        if ($evaluation->getType() == "DEVOIR")
        {
            return $this->render('account/student/evaluation/show_evaluation_claims.html.twig', [
                'registration' => $registration,
                'evaluation' => $evaluation,
                'claims' => $claims,
                'form' => $form->createView()
            ]);
        }
    }
    

}
