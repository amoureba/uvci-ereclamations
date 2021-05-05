<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Entity\Matter;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TeachersRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom(s)'
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail (institutionnel)'
            ])
            ->add('matters', EntityType::class, [
                'class' => Matter::class,
                'choice_label' => 'wording',
                'query_builder' => function (EntityRepository $repo) {return $repo->createQueryBuilder('m')->orderBy('m.wording', 'ASC');},
                'label' => 'Matière(s) enseignée(s)',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('blocked', CheckboxType::class, [
                'label' => 'Bloqué',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
