<?php

namespace App\Components\common;

use App\Entity\Quotation;
use App\Entity\InvoiceItem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: 'components/common/invoice_creator.html.twig')]
class QuotationCreator extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: ['taxe', 'status'])]
    #[Valid]
    public Quotation $quotation;

    #[LiveProp]
    public array $lineItems = [];

    #[LiveProp(writable: true)]
    public ?string $status = null;

    #[LiveProp(writable: true)]
    public array $customers = [];

    #[LiveProp(writable: true)]
    public ?int $selectedCustomerId = null;

    public bool $savedSuccessfully = false;
    public bool $saveFailed = false;
    /* public ?object $selectFormType = null; */

    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    // Injectez CustomerRepository via le constructeur
    public function __construct(ProductRepository $productRepository, CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function mount(Quotation $quotation): void
{
    $this->quotation = $quotation;
    $this->status = $quotation->getStatus();
    $this->selectedCustomerId = $quotation->getCustomer() ? $quotation->getCustomer()->getId() : null;
    $this->lineItems = $this->populateLineItems($quotation);

    $customersCollection = $this->customerRepository->findAll();
    $customersArray = [];
    foreach ($customersCollection as $customer) {
        // Here, you store an array with all the details you need
        $customersArray[$customer->getId()] = [
            'name' => (string) $customer,
            'email' => $customer->getEmail(),
            'address' => $customer->getAddress(),
        ];
    }

    $this->customers = $customersArray;
}

    private function populateLineItems(Quotation $quotation): array
    {
        $lineItems = [];
        foreach ($quotation->getInvoiceItems() as $item) {
            $lineItems[] = [
                'productId' => $item->getProduct()->getId(),
                'quantity' => $item->getQuantity(),
                'discount' => $item->getDiscount(),
                'isEditing' => false,
            ];
        }
        
        return $lineItems;
    }

    #[LiveAction]
    public function addLineItem(): void
    {
        $this->lineItems[] = [
            'productId' => 1,
            'quantity' => 1,
            'discount' => 0,
            'isEditing' => true,
        ];
    }

    #[LiveListener('removeLineItem')]
    public function removeLineItem(#[LiveArg] int $key): void
    {
        unset($this->lineItems[$key]);
    }

    #[LiveListener('line_item:change_edit_mode')]
    public function onLineItemEditModeChange(#[LiveArg] int $key, #[LiveArg] $isEditing): void
    {
        $this->lineItems[$key]['isEditing'] = $isEditing;
    }

    #[LiveListener('line_item:save')]
    public function saveLineItem(#[LiveArg] int $key, #[LiveArg] Product $product, #[LiveArg] int $quantity,#[LiveArg] float $discount ): void
    {
        if (!isset($this->lineItems[$key])) {
            // shouldn't happen
            return;
        }

         $this->lineItems[$key] = [
        'productId' => $product->getId(),
        'quantity' => $quantity,
        'discount' => $discount,
    ];
    }

    #[LiveAction]
    public function saveQuotation(EntityManagerInterface $entityManager)
    {

        $user = $this->getUser();

        if (empty($this->lineItems)) {
            $this->saveFailed = true;
            $this->addFlash('error', 'La facture doit contenir au moins un élément.');
            return;
        }

        if ($this->selectedCustomerId) {
            $customer = $this->customerRepository->find($this->selectedCustomerId);
            $this->quotation->setCustomer($customer);
        }
        $this->saveFailed = true;
        $this->validate();
        $this->saveFailed = false;

         if (!$this->selectedCustomerId) {
        $this->saveFailed = true;
        $this->addFlash('error', 'Un client doit être sélectionné.');
        return; // Arrêtez l'exécution de la méthode ici
    }

        foreach ($this->quotation->getInvoiceItems() as $key => $item) {
            if (!isset($this->lineItems[$key])) {
                // orphanRemoval will cause these to be deleted
                $this->quotation->removeInvoiceItem($item);
            }
        }

        /* Enregistrement des éléments dans la table quotation_item */
        foreach ($this->lineItems as $key => $lineItem) {
            $quotationItem = $this->quotation->getInvoiceItems()->get($key);
            if (null === $quotationItem) {
                $quotationItem = new InvoiceItem();
                $entityManager->persist($quotationItem);
                $this->quotation->addInvoiceItem($quotationItem);
            }

            $product = $this->findProduct($lineItem['productId']);
            $quotationItem->setProduct($product);
            $quotationItem->setQuantity($lineItem['quantity']);
            $quotationItem->setDiscount($lineItem['discount']);
        }
        /* Enregistrer les éléments dans la table quotation */
        $this->quotation->addUser($user);
        $this->quotation->setStatus($this->quotation->getStatus());
        $this->quotation->setTaxe($this->quotation->getTaxe());
        $this->quotation->setTotalWithOutTaxe($this->getSubtotal());
        $this->quotation->setTotal($this->getTotal());

        $isNew = null === $this->quotation->getId();
        $entityManager->persist($this->quotation);
        $entityManager->flush();

        if ($isNew) {
            $this->addFlash('success', 'Facture créée avec succès.');
            return $this->redirectToRoute('app_quotation_index', []);
        }else {
            $this->addFlash('success', 'Facture éditée avec succès.');
            return $this->redirectToRoute('app_quotation_index', []);
        }

        $this->savedSuccessfully = true;

        $this->lineItems = $this->populateLineItems($this->quotation);
        
    }

private function isNewQuotation(): bool
{
    return null === $this->quotation->getId();
}

    private function findProduct(int $id): Product
    {
        return $this->productRepository->find($id);
    }

    public function getTotal(): float
    {
        $taxMultiplier = 1 + ($this->quotation->getTaxe() / 100);

        return $this->getSubtotal() * $taxMultiplier;
    }

    public function getSubtotal(): float
    {
        $subTotal = 0;

        foreach ($this->lineItems as $lineItem) {
            if (!$lineItem['productId']) {
                continue;
            }

            $product = $this->findProduct($lineItem['productId']);

            $subTotal += ($product->getPrice() * $lineItem['quantity'] - $lineItem['discount']);
        }

        return $subTotal;
    }

    public function getAllDiscount(): float
    {
        $discount = 0;

        foreach ($this->lineItems as $lineItem) {
            $discount += $lineItem['discount'];
        }

        return $discount;
    }

    #[ExposeInTemplate]
    public function areAnyLineItemsEditing(): bool
    {
        foreach ($this->lineItems as $lineItem) {
            if ($lineItem['isEditing']) {
                return true;
            }
        }

        return false;
    }

}