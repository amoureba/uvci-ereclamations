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

class ExamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('session', SessionType::class, [
                'label' => 'Session',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Semestre',
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'label' => 'Niveau',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'label' => 'Spécialité',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
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
