<?php

// src/Twig/Components/Alert.php
namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Alert')]
class Alert
{
    public ?string $type = null;
    public ?string $headerMessage = null;
    public ?string $message = null;
    public ?string $action = null;
    public ?string $actionRoute = null;
    public ?string $xText = null;
}