<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

   public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setAgreeTerms(true);
            $user->setIsVerified($faker->boolean(90));
            $user->setRoles([$faker->randomElement(['ROLE_USER', 'ROLE_ADMIN'])]);

            // Encoder le mot de passe
            $password = $this->passwordHasher->hashPassword($user, 'password123');
            $user->setPassword($password);

            $companyDetails = $this->getReference(CompanyDetailsFixtures::COMPANY_DETAILS_REF . '_' . rand(0, 4));
            $user->setCompanyDetails($companyDetails);

            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ThemeFixtures::class,
            CompanyDetailsFixtures::class,
        ];
    }
}