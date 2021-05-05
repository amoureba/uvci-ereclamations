<?php

namespace App\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvaluationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
            'DEVOIR' => 'DEVOIR',
            'EXAMEN' => 'EXAMEN',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}