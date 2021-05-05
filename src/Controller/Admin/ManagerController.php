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

class ManagerController extends AbstractController
{
    /**
     * @Route("/admin/managers/index", name="managers_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $managers = $userRepository->findby(['profile' => 'GESTIONNAIRE']);
        return $this->render('admin/user/manager/index.html.twig', [
            'managers' => $managers,
        ]);
    }

    /**
     * @Route("/admin/managers/add", name="add_managers")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $password = $encoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setProfile('GESTIONNAIRE');
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "<strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_managers');
        }

        return $this->render('admin/user/manager/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression de managers
     * @Route("/admin/managers/{id}/delete", name="delete_managers")
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
                "Le gestionnaire que vous avez sélectionné n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('managers_index');
    }

    /**
     * @Route("/admin/managers/{id}/update", name="update_managers")
     * @return Response
     */
    public function update(User $user, 
    Request $request, 
    EntityManagerInterface $entityManager,
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
            return $this->redirectToRoute('managers_index');
        }

        return $this->render('admin/user/manager/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }


    /** 
     * @Route("/admin/managers/{id}/reset-password", name="reset_managers_password")
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
            return $this->redirectToRoute('managers_index');
        }

        return $this->render('admin/user/manager/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
