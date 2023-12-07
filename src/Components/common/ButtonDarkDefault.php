<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ButtonDarkDefault')]
class ButtonDarkDefault
{
    public string $type;
    public string $text;

}