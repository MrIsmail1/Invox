<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company_logo', TextType::class, [
                'label' => 'Logo de l\'entreprise',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Télécharger le logo de l\'entreprise',
                ],
            ])
            ->add('company_name', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => [
                    'placeholder' => 'Entrez le nom de l\'entreprise',
                ],
            ])
            ->add('company_email', EmailType::class, [
                'label' => 'E-mail de l\'entreprise',
                'attr' => [
                    'placeholder' => 'Entrez l\'e-mail de l\'entreprise',
                ],
                'mapped' => true,
            ])
            ->add('siret_number', TextType::class, [
                'label' => 'Numéro SIRET',
                'attr' => [
                    'placeholder' => 'Entrez le numéro SIRET',
                ],
            ])
            ->add('vat_number', TextType::class, [
                'label' => 'Numéro de TVA',
                'attr' => [
                    'placeholder' => 'Entrez le numéro de TVA',
                ],
            ])
            ->add('vat_exempt', CheckboxType::class, [
                'label' => 'Exonération de TVA',
            ])
            ->add('legal_status', ChoiceType::class, [
                'placeholder' => 'Sélectionnez le statut légal',
                'label' => 'Statut légal',
                'choices' => [
                    'Option 1' => 'option1',
                    'Option 2' => 'option2',
                ],
            ])
            ->add('rcs', TextType::class, [
                'label' => 'Numéro RCS',
                'attr' => [
                    'placeholder' => 'Entrez le numéro RCS',
                ],
            ])
            ->add('default_vat', NumberType::class, [
                'label' => 'Taux de TVA par défaut',
                'attr' => [
                    'placeholder' => 'Entrez le taux de TVA par défaut',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'placeholder' => 'Sélectionnez le pays',
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse',
                ],
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Entrez le code postal',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Entrez la ville',
                ],
            ])
            ->add('website', UrlType::class, [
                'label' => 'Site web',
                'attr' => [
                    'placeholder' => 'Entrez l\'URL du site web',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
