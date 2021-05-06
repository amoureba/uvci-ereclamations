<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserType;
use App\Form\Admin\AllUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\Admin\UserResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * users
     * @Route("/admin/utilisateurs/index", name="users_index")
     */
    public function index(UserRepository $userRepo): Response
    {

        $students = $userRepo->findBy(['profile' => 'ETUDIANT']);
        $teachers = $userRepo->findBy(['profile' => 'ENSEIGNANT']);
        $managers = $userRepo->findBy(['profile' => 'GESTIONNAIRE']);
        /*$technicians = $userRepo->findBy(['profile' => 'TECHNICIEN']);*/

        return $this->render('admin/user/alls/index.html.twig', [
            'students' => $students,
            'teachers' => $teachers,
            'managers' => $managers,
            /*'technicians' => $technicians,*/
        ]);
    }

    /**
     * @Route("/admin/reinitialisation-du-mot-de-passe-de-l-utilisateur/{id}", name="users_reset_password")
     * @return Response
     */
    public function resetPassword(User $user, Request $request, EntityManagerInterface $entityManager, 
    UserPasswordEncoderInterface $encoder)
    {

        $form = $this->createForm(UserResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, 'password'));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le mot de passe de l'utilisateur <strong>{$user->getFullName()}</strong> a été réinitialiser avec succès !"
            );

            return $this->redirectToRoute('users_index');
        }

        return $this->render('admin/user/alls/reset_users_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/ajouter-des-utilisateurs", name="users_add")
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(AllUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //password : password
            $password = $encoder->encodePassword($user, 'password');
            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur a été ajouter avec succès !"
            );
            /*rester sur la même page
            */
            return $this->redirectToRoute('users_add');
        }

        return $this->render('admin/user/alls/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/mise-a-jour-de-l-utilisateur/{id}", name="users_edit")
     * @return Response
     */
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager,
    UserPasswordEncoderInterface $encoder)
    {

        $form = $this->createForm(AllUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('blocked')->getData()) {
                $user->setPassword($encoder->encodePassword($user, uniqid()));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Les modifications ont été ajoutées avec succès !"
            );

            return $this->redirectToRoute('users_index');

        }

        return $this->render('admin/user/alls/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

}
