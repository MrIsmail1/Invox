<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchAutocomplete;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response
    {
        $invoiceItem = new InvoiceItem();
        $query = $invoiceRepository->createQueryBuilder('a')->getQuery();

    $form = $this->createForm(SearchAutocomplete::class);
    $form->handleRequest($request);

    // Initialiser la requête de base pour toutes les quotations
    $queryBuilder = $invoiceRepository->createQueryBuilder('a');

    // Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        $customer = $form->get('customer')->getData();

        // Modifier la requête pour filtrer par client, si un client est sélectionné
        if ($customer) {
            $queryBuilder->andWhere('a.customer = :customer')
                         ->setParameter('customer', $customer);
        }
    }


    $query = $queryBuilder->getQuery();
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
                $discount = $invoiceItem->getDiscount();

                if ($product !== null) {
                    $productData[] = [
                        'name' => $product->getName(),
                        'price' => $product->getPrice(),
                        'category' => $product->getCategory(),
                        'quantity' => $quantity,
                        'discount' => $discount,
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
            'type' => 'invoice',
            'SearchBar' => $form->createView(),
        ]);
    }


    #[Route('invoice/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository, Request $request): Response
    {
        $invoice = new Invoice();
        /* $form = $this->createForm(SelectFormType::class, $invoice);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitez l'enregistrement de l'entité $invoice comme d'habitude
        } */

        $invoiceItem = new InvoiceItem();
        $products = $productRepository->findAll();

        // Pour créer une notification de succès
        $this->addFlash('success', 'La facture a été créée avec succès');

        return $this->render('invoice/page_invoice_new.html.twig', [
            'invoice' => $invoice,
            'invoiceItem' => $invoiceItem,
            'products' => $products,
            'type' => 'invoice',
        ]);
    }

    /* #[Route('invoice/edit/{id}', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
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
    } */
}