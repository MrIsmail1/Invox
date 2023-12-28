<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('expiresIn')
            ->add('title')
            ->add('amount')
            ->add('option')
            ->add('optionPrice')
            ->add('quantity')
            ->add('totalWithOutTaxes')
            ->add('taxes')
            ->add('totalWithTaxes')
            ->add('isValid')
            ->add('users')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
