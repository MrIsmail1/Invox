<?php

namespace App\DataFixtures;

use App\Entity\Quotation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class QuotationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $quotation = new Quotation();

            // Utiliser un timestamp pour createdAt
            $quotation
                ->setCreatedAt(new \DateTimeImmutable())
                ->setExpiresIn($faker->dateTimeInInterval('now', '+1 month'))
                ->setTitle($faker->sentence)
                ->setAmount($faker->randomFloat(2, 100, 1000))
                ->setOption($faker->word)
                ->setOptionPrice($faker->randomFloat(2, 10, 100))
                ->setQuantity($faker->numberBetween(1, 10))
                ->setTotalWithOutTaxes($faker->randomFloat(2, 100, 1000))
                ->setTaxes($faker->randomFloat(2, 5, 20))
                ->setTotalWithTaxes($faker->randomFloat(2, 105, 1020))
                ->setIsValid($faker->boolean);

            $manager->persist($quotation);
        }

        $manager->flush();
    }
}
