<?php

namespace App\Form\Admin;

use App\Entity\Specialty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecialtyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coded', TextType::class, [
                'label' => 'Code de la spécialité',
                'required' => true,
                'help' => 'Ex: BDA',
            ])
            ->add('wording', TextType::class, [
                'label' => 'Libellé de la spécialité',
                'required' => true,
                'help' => 'Ex: Big Data Analytics',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Specialty::class,
        ]);
    }
}
