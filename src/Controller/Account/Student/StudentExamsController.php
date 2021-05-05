<?php

namespace App\Controller\Account\Student;

use App\Entity\Exam;
use App\Entity\Claim;
use App\Form\ClaimType;
use App\Entity\Examination;
use App\Entity\Registration;
use App\Repository\ExamRepository;
use App\Repository\ClaimRepository;
use Symfony\Component\Mime\Address;
use App\Repository\SemesterRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ExaminationRepository;
use App\Repository\RegistrationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class StudentExamsController extends AbstractController
{
    /**
     * Afficher les examens pour une inscription
     * @Route("/compte/etudiant/inscription/{id}/examens", name="students_registrations_exams")
     */
    public function index(Registration $reg, 
    RegistrationRepository $registrationRepo, 
    ExamRepository $examRepo): Response
    {
        $registration = $registrationRepo->find($reg);
        $semester = $registration->getSemester();
        $level = $registration->getLevel();
        $specialty = $registration->getSpecialty();
        $exams = $examRepo->findBy(
            [
                'semester' => $semester,
                'level' => $level,
                'specialty' => $specialty,            
            ]
        );
        return $this->render('account/student/exam/exams.html.twig', [
            'semester' => $semester,
            'level' => $level,
            'specialty' => $specialty,
            'exams' => $exams,
        ]);
    }

    /**
     * Afficher les détails d'un examen
     * @Route("/compte/etudiant/examen/{slug}/details", name="students_exams_details")
     */
    public function details(RegistrationRepository $registrationRepo, 
    ExaminationRepository $examinationRepo, 
    Exam $exam): Response
    {
        $examinations = $examinationRepo->findByExam($exam);
        $registration = $registrationRepo->findOneBy(['user' => $this->getUser(), 'semester' => $exam->getSemester()]);
        return $this->render('account/student/exam/details.html.twig', [
            'exam' => $exam,
            'registration' => $registration,
            'examinations' => $examinations,
        ]);
    }

    /**
     * Afficher une composition par son slug et permettre d'ajouter une reclamation
     * @Route("/compte/etudiant/examen/{slug}/ajouter-reclamation", name="show_examination_and_do_claims")
     * @return Response
     */
    public function show(Examination $examination, 
    ClaimRepository $claimRepo, 
    SemesterRepository $semesterRepo, 
    RegistrationRepository $regRepo, 
    Request $req, 
    EntityManagerInterface $em, 
    SluggerInterface $slugger, 
    MailerInterface $mailer)
    {
        $registration = $regRepo->findOneBy(['user' => $this->getUser(), 'semester' => $examination->getExam()->getSemester()]);
        $claims = $claimRepo->findBy(
            ['examination' => $examination, 'author' => $this->getUser()],
            ['createdAt' => 'DESC']
        );
        $claim = new Claim();
        $form = $this->createForm(ClaimType::class, $claim);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $captureFile = $form['capture']->getData();
            $claimWording = $form['wording']->getData();
            $claimContent = $form['conetnt']->getData();
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
                ->setExamination($examination)
                ->setCategory('EXAMEN')
                ->setArchived(0);
            $em->persist($claim);
            $em->flush();
            /* DEBUT ENVOI MAIL */
            $claimCapturePath = $claim->getCapturePath();
            $from = $this->getUser()->getEmail();
            $fromName = $this->getUser()->getFullName();
            $teachers = $examination->getMatter()->getUsers();
            $subject = '[UVCI e-Reclamation] Nouvelle Réclamation';
            $examinationMatter = $examination->getMatter()->getWording();
            $examSemester = $examination->getExam()->getSemester()->getSlug();
            $examSession = $examination->getExam()->getSession();
            foreach ($teachers as $teacher) 
            {
                $email = (new TemplatedEmail())
                    ->from(new Address($from, $fromName))
                    ->to($teacher->getEmail())
                    ->subject($subject)
                    ->htmlTemplate('emails/notif_exam_claim.html.twig')
                    ->context([
                        'examination_matter' => $examinationMatter,
                        'exam_semester' => $examSemester,
                        'exam_session' => $examSession,
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
            /* FIN ENVOI MAIL */
            $this->addFlash(
                'success',
                "Votre réclamation a été ajoutée avec succès !"
            );
            return $this->redirectToRoute('show_examination_and_do_claims', ['slug' => $examination->getSlug()]);
        }
        return $this->render('account/student/exam/show_examination_claims.html.twig', [
            //'semester' => $semester,
            'registration' => $registration,
            'examination' => $examination,
            'claims' => $claims,
            'form' => $form->createView()
        ]);
    }
}
