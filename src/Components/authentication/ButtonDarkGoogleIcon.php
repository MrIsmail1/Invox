<?php

// src/Twig/Components/Alert.php
namespace App\Components\authentication;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ButtonDarkGoogleIcon')]
class ButtonDarkGoogleIcon
{
    public string $type;
    public string $text;
    public string $icon;

}