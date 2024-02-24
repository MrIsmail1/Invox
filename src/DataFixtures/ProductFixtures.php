<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures
{

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($faker->word);
            $product->setPrice($faker->randomFloat(2, 100, 1000));
            $product->setCategory($faker->word);
            $manager->persist($product);
        }
        $manager->flush();

    }
}