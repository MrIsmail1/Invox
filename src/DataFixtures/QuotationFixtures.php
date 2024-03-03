<?php

namespace App\DataFixtures;

use App\Entity\Quotation;
use App\Entity\InvoiceItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuotationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 300; $i++) {
            $quotation = new Quotation();

            $quotation->setTotalWithOutTaxe($faker->randomFloat(2, 100, 10000));
            $quotation->setTotal($faker->randomFloat(2, 100, 10000));
            $quotation->setTaxe($faker->randomFloat(2, 5, 20));
            $quotation->setIsValid($faker->boolean(90));
            $quotation->setStatus($faker->randomElement([Quotation::STATUS_DRAFT, Quotation::STATUS_DENIED, Quotation::STATUS_ACCEPTED]));

            // Ajouter des InvoiceItems
            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->setProduct($this->getReference('product_' . $faker->numberBetween(0, 49)));
                $invoiceItem->setQuantity($faker->numberBetween(1, 10));
                $invoiceItem->setDiscount($faker->numberBetween(0, 100));

                $manager->persist($invoiceItem);

                $quotation->addInvoiceItem($invoiceItem);
            }

            // Liaison avec un Customer
            $quotation->setCustomer($this->getReference('customer_' . $faker->numberBetween(0, 49)));

            // Liaison avec un User (optionnel, dépend de votre logique d'application)
            $quotation->addUser($this->getReference('user_' . $faker->numberBetween(0, 5)));

            $manager->persist($quotation);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
        ];
    }
}
