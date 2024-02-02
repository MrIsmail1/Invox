<?php

namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Status
{
    public ?string $styles = null;
    public ?string $status = null;
}