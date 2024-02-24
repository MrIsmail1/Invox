<?php

namespace App\Components\common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Modal extends AbstractController
{

    public $data = [];
    public $invoiceItem = [];
    public $products = [];
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;
    public ?string $modal = null;

    public $form = null;

}

