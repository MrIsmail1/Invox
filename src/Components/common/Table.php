<?php

namespace App\Components\common;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Table
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    public array $theads = [];
    public $form = null;
    public $entity = null;
    public $data = null;
    public $invoiceItem = null;
    public $products = null;
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;
    public ?string $modal = null;

}