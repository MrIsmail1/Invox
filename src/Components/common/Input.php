<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Input')]
class Input
{
    public string $type;
    public string $name;
    public string $placeholder;
    public string $value;
    public string $label;
}