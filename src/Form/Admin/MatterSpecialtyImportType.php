<?php

namespace App\Form\Admin;

use App\Entity\Level;
use App\Entity\Specialty;
use App\Entity\MatterSpecialty;
use App\Form\Admin\Type\SemestersType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MatterSpecialtyImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matters', FileType::class, [
            'label' => 'Fichier des matières (.csv)',
            'required' => true,
            'mapped' => false,
            'attr' => ['class' => 'form-control-file'],
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'text/csv',
                        'text/plain'
                    ],
                    'mimeTypesMessage' => 'Choisissez un fichier au format csv et réessayez !',
                ])
            ],
            ])
            ->add('specialty', EntityType::class, [
            'class' => Specialty::class,
            'label' => 'Spécialité',
            'required' => true,
            'attr' => ['class' => 'custom-select'],
            ])
            ->add('level', EntityType::class, [
            'class' => Level::class,
            'label' => 'Niveau',
            'required' => true,
            'attr' => ['class' => 'custom-select'],
            ])
            ->add('semester', SemestersType::class, [
                'label' => 'Semestre',
                'required' => true,
                'attr' => ['class' => 'custom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MatterSpecialty::class,
        ]);
    }
}
