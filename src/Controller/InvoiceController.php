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
use Knp\Component\Pager\PaginatorInterface;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'app_invoice_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response 
    {
    
    $query = $invoiceRepository->createQueryBuilder('a')
    ->getQuery();

    $data = $paginatorInterface->paginate(
        $query,
        $request->query->getInt('page', 1),
        15
    );

    // Collecte des invoiceItems pour chaque invoice
    // Créer un tableau pour stocker les invoice items par ID d'invoice
    $invoiceItem = [];

    // Parcourir chaque invoice dans $data
    // Parcourir chaque invoice dans $data
    foreach ($data as $invoice) {
    // Récupérer l'ID de l'invoice
    $invoiceId = $invoice->getId();

    // Récupérer les invoice items de l'invoice actuel
    $invoiceItems = $invoice->getInvoiceItems();
    
    // Créer un tableau pour stocker les données des produits pour chaque invoice
    $productData = [];

    // Parcourir chaque invoice item de l'invoice actuel
    foreach ($invoiceItems as $invoiceItem) {
        // Accéder aux propriétés de l'objet Product associé à l'InvoiceItem
        $product = $invoiceItem->getProduct();
        $quantity = $invoiceItem->getQuantity();

        // Vérifier si le produit est bien initialisé
        if ($product !== null) {
            // Stocker les données du produit dans un tableau associatif
            $productData[] = [
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'category' => $product->getCategory(),
                'quantity' => $quantity,
            ];
        }
    }
    // Stocker les données des produits associées à l'invoice dans un tableau avec l'ID de l'invoice comme clé
    $productDataByInvoiceId[$invoiceId] = $productData;

    }
        return $this->render('invoice/page_invoice_index.html.twig', [
            'data' => $data,
            'invoiceItem' => $invoiceItem,
            'modal' => "invoiceModal",
            'products' => $productDataByInvoiceId,
        ]);
}

    #[Route('invoice/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository): Response
    {
        $invoice = new Invoice();
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
        ]);
    }


    #[Route('invoice/delete/{id}/{token}', name: 'invoice_delete', methods: ['GET'])]
    public function delete(Invoice $invoice, string $token, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $token)) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}