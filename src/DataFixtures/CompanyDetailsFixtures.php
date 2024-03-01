<?php

namespace App\DataFixtures;

use App\Entity\CompanyDetails;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyDetailsFixtures extends Fixture
{
    public const COMPANY_DETAILS_REF = 'company-details-ref';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $companyDetails = new CompanyDetails();
            $companyDetails->setCompanyName($faker->company);
            $companyDetails->setCompanyEmail($faker->companyEmail);
            $companyDetails->setSiretNumber($faker->numerify('##########'));
            $companyDetails->setVatNumber("FR" . $faker->numerify('##########'));
            $companyDetails->setVatExempt($faker->boolean);
            $companyDetails->setLegalStatus("SARL");
            $companyDetails->setRcs($faker->numerify('##########'));
            $companyDetails->setDefaultVat(20.0);
            $companyDetails->setCountry('FR');
            $companyDetails->setAddress($faker->address);
            $companyDetails->setCity($faker->city);
            $companyDetails->setWebsite($faker->domainName);
            $companyDetails->setPostalCode($faker->postcode);

            // Ajouter des références pour utiliser dans UserFixtures
            $this->addReference(self::COMPANY_DETAILS_REF . '_' . $i, $companyDetails);

            $manager->persist($companyDetails);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
