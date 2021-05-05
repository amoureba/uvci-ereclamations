<?php

namespace App\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
            'SESSION 1' => 'SESSION 1',
            'SESSION 2' => 'SESSION 2',
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}