<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchType;
use App\Form\InvoiceType;
use App\Form\SearchAutocomplete;
use Gedmo\Sortable\Sortable;
use InvoiceFormType;
use Knp\Component\Pager\PaginatorInterface;

class InvoiceController extends AbstractController
{
#[Route('/invoice', name: 'app_invoice_index', methods: ['GET', 'POST'])]
public function index(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response 
{
    $invoiceItem = new InvoiceItem();
    $query = $invoiceRepository->createQueryBuilder('a')->getQuery();

    $data = $paginatorInterface->paginate(
        $query,
        $request->query->getInt('page', 1),
        15
    );

    // Initialiser $productDataByInvoiceId avant de l'utiliser
    $productDataByInvoiceId = [];

    foreach ($data as $invoice) {
        $invoiceId = $invoice->getId();
        $invoiceItems = $invoice->getInvoiceItems();
        $productData = [];

        foreach ($invoiceItems as $invoiceItem) {
            $product = $invoiceItem->getProduct();
            $quantity = $invoiceItem->getQuantity();

            if ($product !== null) {
                $productData[] = [
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'category' => $product->getCategory(),
                    'quantity' => $quantity,
                ];
            }
        }

        $productDataByInvoiceId[$invoiceId] = $productData;
    }

    return $this->render('invoice/page_invoice_index.html.twig', [
        'data' => $data,
        'invoiceItem' => $invoiceItem,
        'modal' => "invoiceModal",
        'products' => $productDataByInvoiceId,
    ]);
}


    #[Route('invoice/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository, Request $request): Response
    {
        $invoice = new Invoice();
        /* $form = $this->createForm(InvoiceFormType::class, $invoice);

        $form->handleRequest($request); */
        
        $invoiceItem = new InvoiceItem();

        $products = $productRepository->findAll();

        // Pour créer une notification de succès
        $this->addFlash('success', 'La facture a été créée avec succès');
        
        return $this->render('invoice/page_invoice_new.html.twig', [
            'invoice' => $invoice,
            'invoiceItem' => $invoiceItem,
            'products' => $products,
        ]);
    }

    #[Route('invoice/edit/{id}', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        return $this->render('invoice/page_invoice_new.html.twig', [
            'invoice' => $invoice,
            'edit' => "edit",
        ]);
    }


    #[Route('invoice/delete/{id}/{token}', name: 'app_invoice_delete', methods: ['GET'])]
    public function delete(Invoice $invoice, string $token, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $token)) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}