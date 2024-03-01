<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName);
            $customer->setLastName($faker->lastName);
            $customer->setEmail($faker->safeEmail);
            $customer->setAddress($faker->address);

            $manager->persist($customer);
            $this->addReference('customer_' . $i, $customer);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            ProductFixtures::class,
        ];
    }
}
