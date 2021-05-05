<?php

namespace App\Form;

use App\Entity\Claim;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClaimType extends AbstractType
{

    // doc upload file : https://symfony.com/doc/current/controller/upload_file.html

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('evaluation', EntityType::class, [
                'class' => Evaluation::class,
                'label' => false,
                'disabled' => true,
                'attr' => ['class' => 'custom-select'],
            ])*/
            ->add('wording', TextType::class, [
                'label' => 'Objet'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Message'
            ])
            ->add('capture', FileType::class, [
            'label' => 'Capture',
            'required' => true,
            'constraints' => [
                new File([
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png'
                    ],
                    'mimeTypesMessage' => 'Choisissez une image (png, jpg, jpeg) et réessayez !',
                ])
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Claim::class,
        ]);
    }
}
