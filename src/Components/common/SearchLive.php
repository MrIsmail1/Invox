<?php

namespace App\Components\common;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template :'components/common/search_live.html.twig')]
class SearchLive
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private CustomerRepository $customerRepository, private InvoiceRepository $invoiceRepository)
    {
    }

    public function getInvoice(): array
    {
        if (empty($this->query)) {
            // Retourner tous les clients si aucune query n'est spécifiée
            return $this->invoiceRepository->findAll();
        }else {
            // Retourner les clients dont le firstname ou lastnam contient la query
             $invoices = $this->invoiceRepository->findBySearchQuery($this->query);
            
            return $invoices;
        }
    }
}
