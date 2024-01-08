<?php

namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Modal
{
    public $data = [];
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;
}