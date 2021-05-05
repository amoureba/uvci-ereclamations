<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClaimTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
            'GESTION' => 'GESTION',
            'TECHNIQUE' => 'TECHNIQUE',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}