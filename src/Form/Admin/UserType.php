<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Form\Admin\Type\ProfileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class,[
                'label' => 'Nom'
            ])
            ->add('firstName', TextType::class,[
                'label' => 'Prénoms'

            ])
            ->add('email', EmailType::class,[
                'label' => 'E-mail'
            ])
            ->add('profile', TextType::class, [
                'label' => 'Profil',
                'disabled' => 'true'
            ])
            ->add('blocked', CheckboxType::class, [
                'label' => 'Bloqué',
                'required' => false
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
