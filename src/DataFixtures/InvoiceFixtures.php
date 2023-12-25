<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $invoice = (new Invoice());

            // Utiliser un timestamp pour createdAt
            
            $invoice->setCreatedAt(new \DateTimeImmutable());
            $invoice->setExpiresIn($faker->dateTimeBetween('now', '+1 year'));
            $invoice->setAmount($faker->randomFloat(2, 10, 1000));
            $invoice->setTitle($faker->sentence);
            $invoice->setOption($faker->word);
            $invoice->setOptionPrice($faker->randomFloat(2, 5, 200));
            $invoice->setQuantity($faker->numberBetween(1, 100));
            $invoice->setTotalWithOutTaxes($faker->randomFloat(2, 50, 100));
            $invoice->setTaxes($faker->randomFloat(2, 5, 20));
            $invoice->setTotalWithTaxes($faker->randomFloat(2, 50, 100));
            $invoice->setIsValid($faker->boolean(50));

            // Liaison avec une Quotation (assurez-vous que QuotationFixture est exécutée avant InvoiceFixtures)
            /* $quotation = $this->getReference('quotation_' . $faker->numberBetween(1, 10));
            $invoice->setQuotation($quotation); */

            $manager->persist($invoice);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            QuotationFixtures::class,
        ];
    }
}
