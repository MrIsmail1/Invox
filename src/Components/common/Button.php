<?php

namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Button
{
    public ?string $path=null;
    public ?string $content=null;
    public ?string $styles = null;
    public ?string $js = null;
    public ?string  $dataAction = null;
    public ?string  $dataActionName = null;
    
}