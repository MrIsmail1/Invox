<?php

namespace App\Components\dashboard;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class CardDashboard
{
    public string $src;
    public string $number;
    public string $title;
}