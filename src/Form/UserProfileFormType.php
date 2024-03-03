<?php

namespace App\Form;

use App\Entity\Theme;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('media', MediaFormType::class, [
                'label' => false,
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'name',
                'choice_value' => 'id', // Assuming 'slug' is a unique identifier for the theme, like 'default', 'pikachu', 'girly'.
                'label' => "Thème de l'interface",
                'placeholder' => 'Sélectionnez un thème',
                'required' => true,
                'expanded' => false,
                'multiple' => false, // Set to false because you want the user to select only one theme.
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
