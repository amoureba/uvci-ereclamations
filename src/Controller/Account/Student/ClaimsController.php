<?php

namespace App\Controller\Account\Student;

use App\Entity\Claim;
use App\Form\OtherClaimType;
use App\Repository\UserRepository;
use App\Repository\ClaimRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ClaimsController extends AbstractController
{
    /**
     * @Route("/compte/etudiant/ajouter-reclamations-autre-categorie", name="add_other_category_claim")
     */
    public function add(Request $request, 
    EntityManagerInterface $entityManager, 
    SluggerInterface $slugger, UserRepository $userRepo, 
    MailerInterface $mailer): Response
    {
        $claim = new Claim();
        $form = $this->createForm(OtherClaimType::class, $claim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $captureFile = $form->get('capture')->getData();
            /*$claimCategory = $form->get('category')->getData();*/
            $claimWording = $form->get('wording')->getData();
            $claimContent = $form->get('content')->getData();
            /* SI CAPTURE */
            if ($captureFile) {
                $originalFilname = pathinfo($captureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilname);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $captureFile->guessExtension();
                $captureFile->move($this->getParameter('captures_directory'), $newFilename);
                $claim->setCapture($newFilename);
            }
            /* FIN SI CAPTURE */
            $claim->setCategory('AUTRE');
            $claim->setAuthor($this->getUser());
            $entityManager->persist($claim);
            $entityManager->flush();
            /* DEBUT ENVOI DE MAIL */
            $claimCapturePath = $claim->getCapturePath();
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            /*$profile = 'GESTIONNAIRE';
            if($claimCategory == 'TECHNIQUE')
            {
                $profile = 'TECHNICIEN';
            }
            $tos = $userRepo->findby(['profile' => $profile]);*/
            $tos = $userRepo->findby(['profile' => 'GESTIONNAIRE']);
            $subject = '[UVCI e-Reclamation] Nouvelle Réclamation';
            foreach ($tos as $to) {
                $email = (new TemplatedEmail())
                    ->from(new Address($from, $fromName))
                    ->to($to->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('emails/notif_other_claim.html.twig')
                    ->context([
                        'claim_wording' => $claimWording,
                        'claim_content' => $claimContent,
                        'claim_capture_path' => $claimCapturePath
                    ]);
                try 
                {
                    $mailer->send($email);
                } 
                catch (TransportExceptionInterface $e) {}
            }
            /* FIN ENVOI DE MAIL */
            $this->addFlash(
                'success',
                "Votre réclamation a été ajoutée avec succès !!"
            );
            
            /* shwo claim */
            return $this->redirectToRoute('claim_other_category_show', [
                'id' => $claim->getId()
            ]);
        }

        return $this->render('account/student/claim/other.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/etudiant/editer-reclamation-autre-categorie/{id}", name="edit_other_category_claims")
     * @return Response
     */
    public function edit(Claim $claim, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    SluggerInterface $slugger)
    {
        $form = $this->createForm(OtherClaimType::class, $claim);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $captureFile = $form->get('capture')->getData();
            if ($captureFile) 
            {
                $originalFilname = pathinfo($captureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilname);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $captureFile->guessExtension();
                $captureFile->move($this->getParameter('captures_directory'), $newFilename);
                $claim->setCapture($newFilename);
            }
            $entityManager->persist($claim);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Vos Modifications ont été ajoutées !"
            );
            return $this->redirectToRoute('claim_other_category_show', [
                'id' => $claim->getId()
            ]);
        }

        return $this->render('account/student/claim/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/etudiant/afficher-reclamation-autre-categorie/{id}", name="claim_other_category_show")
     * @return Response
     */
    public function show(Claim $claim)
    {
        return $this->render('account/student/claim/show.html.twig', [
            'claim' => $claim
        ]);
    }
}