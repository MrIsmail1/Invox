<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $themes = [
            ['value' => 'default', 'name' => 'default'],
            ['value' => 'pikachu', 'name' => 'pikachu'],
            ['value' => 'girl', 'name' => 'girl'],
        ];

        foreach ($themes as $themeData) {
            $theme = new Theme();
            $theme->setValue($themeData['value']);
            $theme->setName($themeData['name']);
            $manager->persist($theme);
        }

        $manager->flush();
    }
}
