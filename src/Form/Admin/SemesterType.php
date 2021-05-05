<?php

namespace App\Form\Admin;

use App\Entity\Semester;
use App\Entity\AcademicYear;
use App\Form\Admin\Type\SemestersType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SemesterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording', SemestersType::class, [
                'label' => 'Libellé du semestre',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('coded', TextType::class, [
                'label' => 'Code du semestre',
                'help' => 'Ex: SEMESTRE 1 => S1',
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'help' => 'Ex: 01/01/2020',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'help' => 'Ex: 30/04/2020',
            ])
            ->add('academicYear', EntityType::class, [
                'class' => AcademicYear::class,
                'label' => 'Rentrée Academique',
                'attr' => ['class' => 'custom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Semester::class,
        ]);
    }
}
