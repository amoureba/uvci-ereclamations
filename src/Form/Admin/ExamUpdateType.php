<?php

namespace App\Form\Admin;

use App\Entity\Exam;
use App\Entity\Level;
use App\Entity\Semester;
use App\Entity\Specialty;
use App\Form\Admin\Type\SessionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ExamUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('session', SessionType::class, [
                'label' => 'Session',
                'required' => false,
                'attr' => ['class' => 'custom-select'],
                'disabled' => true
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Semestre',
                'required' => false,
                'attr' => ['class' => 'custom-select'],
                'disabled' => true
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'label' => 'Niveau',
                'required' => false,
                'attr' => ['class' => 'custom-select'],
                'disabled' => true
            ])
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'label' => 'Spécialité',
                'required' => false,
                'attr' => ['class' => 'custom-select'],
                'disabled' => true
            ])
            ->add('archived', CheckboxType::class, [
                'label' => 'Archivé',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
        ]);
    }
}
