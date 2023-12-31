<?php
// src/Components/RandomNumber.php
namespace App\Components\common;

use App\Entity\Invoice;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent()]
class DivLive
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $productCount = 30;

    #[LiveProp]
    public string $lastProduct = "";

    #[LiveListener('productAdded')]
    public function incrementProductCount()
    {
        $this->productCount++;
    }
}