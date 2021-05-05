<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Form\ApplicationType;
use App\Form\Settings\Type\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Nom de famille"))
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom(s)", "Prénoms..."))
            ->add('email', EmailType::class, $this->getConfiguration("E-mail", "E-mail institutionnelle"))
            ->add('password', PasswordType::class, $this->getConfiguration("Mot de passe", "Mot de passe..."))
            ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmez le mot de passe", "Confirmez le mot de passe ici"))
            ->add('profile', ProfileType::class, [
                'label' => 'Profil',
                'attr' => ['class' => 'custom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
