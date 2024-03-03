<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, options: [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le mot de passe ne peut pas être vide',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'attr' => ['placeholder' => 'Veuillez saisir votre mot de passe...']
                ],
                'second_options' => [
                    'label' => 'Confirmation de mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre mot de passe...']],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
