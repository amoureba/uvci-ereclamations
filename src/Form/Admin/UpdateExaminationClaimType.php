<?php

namespace App\Form\Admin;

use App\Entity\Claim;
use App\Entity\Evaluation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UpdateExaminationClaimType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording', TextType::class, [
                'label' => 'Objet'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message'
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
            'data_class' => Claim::class,
        ]);
    }
}
