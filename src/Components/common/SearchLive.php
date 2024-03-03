<?php

namespace App\Components\common;

use App\Repository\InvoiceRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;

#[AsLiveComponent(template :'components/common/search_live.html.twig')]
class SearchLive
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(private InvoiceRepository $invoiceRepository)
    {
    }

    #[LiveAction]
    public function getInvoice(): array
    {
        if (empty($this->query)) {
            // Retourner tous les clients si aucune query n'est spécifiée
            return $this->invoiceRepository->findAll();
        } else {
            // Retourner les clients dont le firstname ou lastnam contient la query
            $invoices = $this->invoiceRepository->findBySearchQuery($this->query);

            // Convertir les objets Invoice en tableaux associatifs
            $formattedInvoices = [];
            foreach ($invoices as $invoice) {
                $formattedInvoices[] = [
                    'id' => $invoice->getId(),
                    'customer' => $invoice->getCustomer(),
                    'total' => $invoice->getTotalWithTaxes(),
                    'status' => $invoice->isIsValid(),
                    'createdAt' => $invoice->getExpiresIn(),
                    'expiresIn' => $invoice->getExpiresIn(),
                ];
            }
            
            $this->emit('invoicesFetched', [
                'invoice' => $formattedInvoices
            ]);
            return $formattedInvoices;
        }
    }
}