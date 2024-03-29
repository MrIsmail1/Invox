<?php

namespace App\Components\common;

use App\Entity\Invoice;
use App\Entity\CompanyDetails;
use App\Entity\InvoiceItem;
use App\Entity\Product;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsLiveComponent(template: 'components/common/invoice_creator.html.twig')]
class InvoiceCreator extends AbstractController
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: ['taxe', 'status'])]
    #[Valid]
    public Invoice $invoice;

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
    private $tokenStorage;

    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    // Injectez CustomerRepository via le constructeur
    public function __construct(ProductRepository $productRepository, CustomerRepository $customerRepository, TokenStorageInterface $tokenStorage)
    {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function mount(Invoice $invoice): void
    {
        $user = $this->getUser();
        $this->invoice = $invoice;
        $this->status = $invoice->getStatus();
        $this->selectedCustomerId = $invoice->getCustomer() ? $invoice->getCustomer()->getId() : null;
        $this->lineItems = $this->populateLineItems($invoice);

        $customersCollection = $user->getCustomers();
        $customersArray = [];
        foreach ($customersCollection as $customer) {
            // Here, you store an array with all the details you need
            $customersArray[$customer->getId()] = [
                'name' => (string)$customer,
                'email' => $customer->getEmail(),
                'address' => $customer->getAddress(),
            ];
        }

        $this->customers = $customersArray;
    }

    private function populateLineItems(Invoice $invoice): array
    {
        $lineItems = [];
        foreach ($invoice->getInvoiceItems() as $item) {
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
            'productId' => null,
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
    public function saveLineItem(#[LiveArg] int $key, #[LiveArg] Product $product, #[LiveArg] int $quantity, #[LiveArg] float $discount): void
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
    public function saveInvoice(EntityManagerInterface $entityManager)
    {

        $user = $this->getUser();

        if (empty($this->lineItems)) {
            $this->saveFailed = true;
            $this->addFlash('error', 'La facture doit contenir au moins un élément.');
            return;
        }

        if ($this->selectedCustomerId) {
            $customer = $this->customerRepository->find($this->selectedCustomerId);
            $this->invoice->setCustomer($customer);
        }
        $this->saveFailed = true;
        $this->validate();
        $this->saveFailed = false;

        if (!$this->selectedCustomerId) {
            $this->saveFailed = true;
            $this->addFlash('error', 'Un client doit être sélectionné.');
            return; // Arrêtez l'exécution de la méthode ici
        }

        foreach ($this->invoice->getInvoiceItems() as $key => $item) {
            if (!isset($this->lineItems[$key])) {
                // orphanRemoval will cause these to be deleted
                $this->invoice->removeInvoiceItem($item);
            }
        }

        /* Enregistrement des éléments dans la table invoice_item */
        foreach ($this->lineItems as $key => $lineItem) {
            $invoiceItem = $this->invoice->getInvoiceItems()->get($key);
            if (null === $invoiceItem) {
                $invoiceItem = new InvoiceItem();
                $entityManager->persist($invoiceItem);
                $this->invoice->addInvoiceItem($invoiceItem);
            }

            $product = $this->findProduct($lineItem['productId']);
            $invoiceItem->setProduct($product);
            $invoiceItem->setQuantity($lineItem['quantity']);
            $invoiceItem->setDiscount($lineItem['discount']);
        }

        /* Enregistrer les éléments dans la table invoice */
        $this->invoice->addUser($user);
        $this->invoice->setStatus($this->invoice->getStatus());
        $this->invoice->setTaxe($this->invoice->getTaxe());
        $this->invoice->setTotalWithOutTaxe($this->getSubtotal());
        $this->invoice->setTotal($this->getTotal());

        $isNew = null === $this->invoice->getId();
        $entityManager->persist($this->invoice);
        $entityManager->flush();

        if ($isNew) {
            $this->addFlash('success', 'Facture créée avec succès.');
            return $this->redirectToRoute('app_invoice_index', []);
        } else {
            $this->addFlash('success', 'Facture éditée avec succès.');
            return $this->redirectToRoute('app_invoice_index', []);
        }

        $this->savedSuccessfully = true;

        $this->lineItems = $this->populateLineItems($this->invoice);

    }

    private function findProduct(int $id): Product
    {
        return $this->productRepository->find($id);
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

    public function getTotal(): float
    {
        $taxMultiplier = 1 + ($this->invoice->getTaxe() / 100);

        return $this->getSubtotal() * $taxMultiplier;
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

    private function isNewInvoice(): bool
    {
        return null === $this->invoice->getId();
    }

    #[ExposeInTemplate]
    public function getCompanyDetails(): ?CompanyDetails
    {
        $token = $this->tokenStorage->getToken();

        $user = $token->getUser();

        return $user->getCompanyDetails();
    }

}