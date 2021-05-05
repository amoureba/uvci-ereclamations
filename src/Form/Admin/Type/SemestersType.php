<?php

namespace App\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SemestersType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
            'SEMESTRE 1' => 'SEMESTRE 1',
            'SEMESTRE 2' => 'SEMESTRE 2',
            'SEMESTRE 3' => 'SEMESTRE 3',
            'SEMESTRE 4' => 'SEMESTRE 4',
            'SEMESTRE 5' => 'SEMESTRE 5',
            'SEMESTRE 6' => 'SEMESTRE 6',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}