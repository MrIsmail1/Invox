<?php

namespace App\Components\dashboard;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/dashboard/card_dashboard.html.twig')]
class CardDashboard
{
    public string $picto;
    public string $number;
    public string $title;
}