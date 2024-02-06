<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Adresse e-mail'],
                'label' => 'E-mail',
                'mapped' => true,
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['placeholder' => 'Entrez votre prénom'],
                'label' => 'Prénom',
                'mapped' => true,
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['placeholder' => 'Entrez votre nom de famille'],
                'label' => 'Nom de famille',
                'mapped' => true,
                'required' => false,
            ])
            ->add('job', TextType::class, [
                'attr' => ['placeholder' => 'Entrez votre profession'],
                'label' => 'Profession',
                'mapped' => true,
                'required' => false,
            ])
            ->add('media', CollectionType::class, [
                'entry_type' => MediaFormType::class,
                'label' => false,
                'mapped' => true,
                'required' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
