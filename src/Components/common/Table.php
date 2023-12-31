<?php

namespace App\Components\common;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
#[AsLiveComponent]
class Table
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    public ?string $thead1 = null;
    public ?string $thead2 = null;
    public ?string $thead3 = null;
    public ?string $thead4 = null;
    public ?string $thead5 = null;
    public ?string $thead6 = null;
    public ?string $thead7 = null;
    public $form = null;
    public $entity = null;
    public ?array $data = null;
    public ?string $pathDelete = null;
    public ?string $pathEdit = null;

     // Déclarer le type de $data comme array
    #[LiveProp]
    public ?array $Hamza = null;


    // Écouter l'événement 'invoicesFetched'
    #[LiveListener('invoicesFetched')]
    public function handleInvoicesFetched(#[LiveArg] array $invoices)
    {
        // Mettre à jour les données avec celles reçues dans l'événement
        $this->Hamza = $invoices;
    }

}