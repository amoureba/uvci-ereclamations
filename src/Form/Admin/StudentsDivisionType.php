<?php

namespace App\Form\Admin;

use App\Entity\Level;
use App\Entity\Semester;
use App\Entity\Specialty;
use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class StudentsDivisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EmailType::class, [
                'label' => "E-mail de l'étudiant",
                'required' => true,
                'mapped' => false,
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'label' => 'Niveau',
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'label' => 'Spécialité',
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Semestre',
                'attr' => ['class' => 'custom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
