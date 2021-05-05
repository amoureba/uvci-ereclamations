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

class OtherClaimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('category', ClaimTypeType::class, [
                'label' => 'Catégorie',
                'attr' => ['class' => 'custom-select'],
            ])*/
            ->add('wording', TextType::class, [
                'label' => 'Objet / Sujet'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('capture', FileType::class, [
                'label' => 'Joindre fichier / capture',
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
