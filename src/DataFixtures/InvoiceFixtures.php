<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $invoice = new Invoice();
            // Configuration de l'invoice
            $invoice->setTotalWithOutTaxe($faker->randomFloat(2, 100, 10000));
            $invoice->setTotal($faker->randomFloat(2, 100, 10000));
            $invoice->setTaxe($faker->randomFloat(2, 5, 20));
            $invoice->setIsValid($faker->boolean(90));
            $invoice->setStatus($faker->randomElement([Invoice::STATUS_UNPAID, Invoice::STATUS_PAID, Invoice::STATUS_OVERDUE]));

            // Ajout des InvoiceItems
            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->setProduct($this->getReference('product_' . $faker->numberBetween(0, 49)));
                $invoiceItem->setQuantity($faker->numberBetween(1, 10));
                $invoiceItem->setDiscount($faker->numberBetween(0, 100));
                $invoice->addInvoiceItem($invoiceItem);
                $manager->persist($invoiceItem);
            }

            $customerRefIndex = $faker->numberBetween(0, 9);
            $invoice->setCustomer($this->getReference('customer_' . $customerRefIndex));

            
            $invoice->addUser($this->getReference('user_' . $faker->numberBetween(0, 5)));

            $manager->persist($invoice);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ProductFixtures::class,
            QuotationFixtures::class,
        ];
    }
}
