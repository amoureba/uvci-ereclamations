<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use App\Repository\UserRepository;
use App\Form\Admin\SearchEmailType;
use App\Form\SearchUserByEmailType;
use App\Form\Admin\UserRegisterType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\UserResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class AdministratorController extends AbstractController
{
    /**
     * @Route("/admin/administrators/index", name="administrators_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $administrators = $userRepository->findby(['profile' => 'ADMINISTRATEUR']);
        return $this->render('admin/user/administrator/index.html.twig', [
            'administrators' => $administrators,
        ]);
    }

    /**
     * @Route("/admin/administrators/add", name="add_administrators")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $user->setProfile('ADMINISTRATEUR');
            $user->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "<strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_administrators');
        }
        return $this->render('admin/user/administrator/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/supers-administrators/add", name="add_supers_administrators")
     * @return Response
     */
    public function addSuperAdmin(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $user->setProfile('ADMINISTRATEUR');
            $user->setRoles(['ROLE_SUPER_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "<strong>{$user->getFullName()}</strong> a été ajouté avec succès !"
            );
            return $this->redirectToRoute('add_supers_administrators');
        }
        return $this->render('admin/user/administrator/add_super_admin.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/designate-administrators", name="designate_administrators")
     * @return Response
     */
    public function assignAdmin(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        /* $user = new User(); */
        $form = $this->createForm(SearchUserByEmailType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            /* $user = $userRepository->findOneByEmail($email); */
            if ($user = $userRepository->findOneByEmail($email)) {
                $user->setRoles(['ROLE_ADMIN']);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    "L'affectation a été éffectuée avec succès !"
                );
            } else {
                $this->addFlash(
                    'danger',
                    "Echèc de l'opération, email non attribué !"
                );
                return $this->redirectToRoute('designate_administrators');
            }
            return $this->redirectToRoute('administrators_index');
        }
        return $this->render('admin/user/administrator/designate_admin.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/administrators/{id}/update", name="update_administrators")
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
            return $this->redirectToRoute('administrators_index');
        }
        return $this->render('admin/user/administrator/update.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/administrators/{id}/delete", name="delete_administrators")
     * @return Response
     */
    public function delete(
        User $user,
        EntityManagerInterface $entityManager,
        UserRepository $userRepo
    ) {
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
                "L'administrateur que vous avez choisis n'existe pas dans la base de données !"
            );
        }
        return $this->redirectToRoute('administrators_index');
    }

    /** 
     * @Route("/admin/administrators/{id}/reset-password", name="reset_administrators_password")
     * @return Response
     */
    public function resetPassword(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
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
            return $this->redirectToRoute('administrators_index');
        }
        return $this->render('admin/user/administrator/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
