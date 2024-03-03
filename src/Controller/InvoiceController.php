<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Form\SearchAutocomplete;
use App\Repository\InvoiceRepository;
use App\Repository\ProductRepository;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response
    {

        $user = $this->getUser();
        $companyDetails = $user->getCompanyDetails();
        $entityManager->initializeObject($companyDetails);

        $invoiceItem = new InvoiceItem();
        /* $query = $invoiceRepository->createQueryBuilder('a')->getQuery(); */
        // Initialiser la requête de base pour les invoices de l'utilisateur
        $queryBuilder = $invoiceRepository->createQueryBuilderForUser($user);

        // Search bar
        $form = $this->createForm(SearchAutocomplete::class);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->get('customer')->getData();

            // Modifier la requête pour filtrer par client, si un client est sélectionné
            if ($customer) {
                $queryBuilder->andWhere('a.customer = :customer')
                    ->setParameter('customer', $customer);
            }
        }


        $data = $paginatorInterface->paginate(
            $queryBuilder->getQuery(),
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
            'pathExport' => 'app_invoice_export',
            'companyDetails' => $companyDetails,
            'pathEdit' => 'app_invoice_edit',
            'pathDelete' => 'app_invoice_delete',
        ]);
    }


    #[Route('invoice/pdf/{id}', name: 'app_invoice_export', methods: ['GET', 'POST'])]
    public function generatePdfInvoice(Invoice $invoice = null, PdfService $pdf, EntityManagerInterface $entityManager): Response
    {


        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas');
        }

        $user = $this->getUser();
        $companyDetails = $user->getCompanyDetails();
        $entityManager->initializeObject($companyDetails);

        $customer = $invoice->getCustomer();
        $entityManager->initializeObject($customer);

        // Récupérer les données nécessaires pour cette invoice spécifique
        $invoiceItems = $invoice->getInvoiceItems();
        $productData = [];
        /* $productDataByInvoiceId = []; */

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

        $response = $this->render('invoice/page_invoice_pdf.html.twig', [
            'products' => $productData,
            'data' => $invoice,
            'companyDetails' => $companyDetails,
            'customer' => $customer,
        ]);
        $html = $response->getContent();
        $pdfContent = $pdf->generateBinaryPDF($html); // Récupérer le contenu du PDF

        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice.pdf"'
        ]);

    }


    #[Route('invoice/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository, Request $request): Response
    {

        $user = $this->getUser();
        $invoice = new Invoice();
        $invoiceItem = new InvoiceItem();
        $products = $user->getProducts();

        // Pour créer une notification de succès
        $this->addFlash('success', 'La facture a été créée avec succès');

        return $this->render('invoice/page_invoice_new.html.twig', [
            'invoice' => $invoice,
            'invoiceItem' => $invoiceItem,
            'products' => $products,
            'type' => 'invoice',
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