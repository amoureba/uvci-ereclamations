<?php

namespace App\Form\Admin;

use App\Entity\Matter;
use App\Entity\Semester;
use App\Entity\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matter', EntityType::class, [
                'class' => Matter::class,
                'label' => 'Matière',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('wording', TextType::class, [
                'label' => 'Libellé'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Semestre',
                'attr' => ['class' => 'custom-select'],
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
            'data_class' => Evaluation::class,
        ]);
    }
}
