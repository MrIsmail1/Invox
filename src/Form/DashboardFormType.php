<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DashboardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('start_date', DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Date de début',
                // Ajoutez ici d'autres options si nécessaire
            ])
            ->add('end_date', DateType::class, [
                'widget' => 'single_text', 
                'label' => 'Date de fin',
                // Ajoutez ici d'autres options si nécessaire
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
