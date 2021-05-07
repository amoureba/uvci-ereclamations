<?php

namespace App\Form\Admin;

use App\Entity\Level;
use App\Entity\Semester;
use App\Entity\Specialty;
use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StudentsDivisionForImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', FileType::class, [
            'label' => 'Sélectionnez le fichier des étudiants au format csv utf-8 (délimité par des virgules)',
            'required' => true,
            'mapped' => false,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'text/csv',
                        'text/plain'
                    ],
                    'mimeTypesMessage' => 'Choisissez un fichier au format csv utf-8 (délimité par des virgules) et réessayez !',
                ])
            ],
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'label' => 'Niveau',
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'label' => 'Spécialité',
                'attr' => ['class' => 'custom-select'],
            ])
            ->add('semester', EntityType::class, [
                'class' => Semester::class,
                'label' => 'Semestre',
                'attr' => ['class' => 'custom-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
