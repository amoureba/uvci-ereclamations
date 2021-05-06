<?php

namespace App\Controller\Admin;

use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Form\UpdateAccountType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminAccountController extends AbstractController
{

    /**
     * Mise a jour (nom, prenoms et email) de l'administrateur en ligne
     * @Route("/admin/mise-a-jour-profil-utilisateur", name="update_admin_account")
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger)
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
                "Les modifications ont été affectuées avec succès !"
            );

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/account/update_account.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modification mot de passe administrateur en ligne
     * @Route("/admin/modification-mot-de-passe", name="update_admin_password")
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();

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

                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('admin/account/update_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
