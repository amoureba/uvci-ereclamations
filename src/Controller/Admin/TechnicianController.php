<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use App\Repository\UserRepository;
use App\Form\Admin\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\UserResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TechnicianController extends AbstractController
{
    /**
     * @Route("/admin/techniciens/index", name="technicians_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $technicians = $userRepository->findby(['profile' => 'TECHNICIEN']);
        return $this->render('admin/user/technician/index.html.twig', [
            'technicians' => $technicians,
        ]);
    }

    /**
     * @Route("/admin/ajouter-des-techniciens", name="add_technicians")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $user->setProfile('TECHNICIEN');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "<strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_technicians');
        }
        return $this->render('admin/user/technician/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-du-technicien/{id}", name="update_technicians")
     * @return Response
     */
    public function update(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
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
            return $this->redirectToRoute('technicians_index');
        }
        return $this->render('admin/user/technician/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Suppression de techniciens
     * @Route("/admin/supprimer-le-technicien/{id}", name="delete_technicians")
     * @return Response
     */
    public function delete(User $user, UserRepository $userRepo, EntityManagerInterface $entityManager)
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
                "Le technicien que vous avez sélectionné n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('technicians_index');
    }

    /** 
     * @Route("/admin/reinitialisation-du-mot-de-passe-du-technicien/{id}", name="reset_technicians_password")
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
            return $this->redirectToRoute('technicians_index');
        }
        return $this->render('admin/user/technician/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
