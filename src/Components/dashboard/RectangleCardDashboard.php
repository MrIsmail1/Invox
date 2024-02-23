<?php

namespace App\Components\dashboard;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/dashboard/rectangle_card_dashboard.html.twig')]
class RectangleCardDashboard
{
    public string $title;
    public string $number;
    public string $picto;
}