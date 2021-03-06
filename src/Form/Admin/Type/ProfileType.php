<?php

namespace App\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
            'ENSEIGNANT' => 'ENSEIGNANT',
            'ETUDIANT' => 'ETUDIANT',
            'GESTIONNAIRE' => 'GESTIONNAIRE',
            /*'TECHNICIEN' => 'TECHNICIEN',*/
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}