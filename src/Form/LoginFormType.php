<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', type: EmailType::class, options: [
                'label' => 'Email',
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre email...',
                ],
            ])
            ->add('password', type: PasswordType::class, options: [
                'label' => 'Mot de passe',
                'block_name' => 'password',
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe...',
                ],
            ])
            ->add('remember_me', type: CheckboxType::class, options: [
                'label' => 'Se souvenir de moi',
                'block_name' => 'remember_me',
                'mapped' => true,
                'attr' => [
                    'placeholder' => 'Se souvenir de moi',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
