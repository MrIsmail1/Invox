<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Head
{
    public string $title;
}