<?php
// src/Components/RandomNumber.php
namespace App\Components\common;

use App\Repository\InvoiceRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent()]
class RandomNumber
{

    public function __construct(private InvoiceRepository $invoiceRepository)
    {
    }
    
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    #[LiveAction]
    public function saveProduct()
    {
        $this->emit('productAdded');
    }
}