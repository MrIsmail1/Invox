<?php

namespace App\Components\common;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Modal
{
    public $data = [];
    public $invoiceItem = [];
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;
    public ?string $modal = null;
}