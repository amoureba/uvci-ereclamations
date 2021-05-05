<?php

namespace App\Form\Admin;

use App\Entity\Matter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coded', TextType::class, [
                'label' => 'Code de la matière',
                'help' => 'Ex: CAS2107'
            ])
            ->add('wording', TextType::class, [
                'label' => 'Libelle de la matière',
                'help' => 'Ex: Calcul Scientifique'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matter::class,
        ]);
    }
}
