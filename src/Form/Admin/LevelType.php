<?php

namespace App\Form\Admin;

use App\Entity\Level;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LevelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('coded', TextType::class, [
                'label' => 'Code du niveau',
                'help' => 'Ex: L1',
            ])
            ->add('wording', TextType::class, [
                'label' => 'Libelle du niveau',
                'help' => 'Ex: Licence 1'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Level::class,
        ]);
    }
}
