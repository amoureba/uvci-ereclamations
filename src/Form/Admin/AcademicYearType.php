<?php

namespace App\Form\Admin;

use App\Entity\AcademicYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AcademicYearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coded', TextType::class, [
                'label' => 'Code de la rentrée',
                'help' => 'Ex: RAJ 2021'
            ])
            ->add('wording', TextType::class, [
                'label' => 'Libelle de la rentrée',
                'help' => 'Ex: RENTREE JANVIER 2021'
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'help' => 'Ex: 01/01/2020'
            ])
            ->add('endDate', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
                'help' => 'Ex: 01/12/2020'
            ])
            ->add('archived', CheckboxType::class, [
                'label' => 'Archivée',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AcademicYear::class,
        ]);
    }
}
