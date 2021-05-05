<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Nom de famille..."))
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom(s)", "Prénoms..."))
            ->add('email', EmailType::class, $this->getConfiguration("E-mail", "E-mail institutionnelle..."))
            ->add('profile')
            //->add('avatar', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
