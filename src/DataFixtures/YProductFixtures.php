<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class YProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = ['produit', 'service'];

        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setName($faker->word);
            $product->setPrice($faker->numberBetween(100, 10000));
            $product->setCategory($faker->randomElement($categories));

            $randomUserId = rand(1, 9);
            $user = $this->getReference('user_' . $randomUserId);
            $product->setUsers($user);
            
            $manager->persist($product);
            $this->addReference('product_' . $i, $product);
        }

        $manager->flush();
    }

}
