<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture
{
    public const THEME_REF = 'theme';

    public function load(ObjectManager $manager): void
    {
        $themes = [
            ['value' => 'default', 'name' => 'Par défaut'],
            ['value' => 'pikachu', 'name' => 'Pikachu'],
            ['value' => 'girl', 'name' => 'Girl'],
        ];

        foreach ($themes as $index => $themeData) {
            $theme = new Theme();
            $theme->setValue($themeData['value']);
            $theme->setName($themeData['name']);
            $manager->persist($theme);
            $this->addReference(self::THEME_REF . '_' . $index, $theme);
        }

        $manager->flush();
    }
}
