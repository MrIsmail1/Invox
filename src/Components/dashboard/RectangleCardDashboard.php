<?php

namespace App\Components\dashboard;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class RectangleCardDashboard
{
    public string $title;
    public string $number;
    public string $stats;
}