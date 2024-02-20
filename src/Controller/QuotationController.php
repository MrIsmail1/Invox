<?php

namespace App\Controller;

use App\Entity\Quotation;
use App\Entity\InvoiceItem;
use App\Repository\ProductRepository;
use App\Repository\QuotationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class QuotationController extends AbstractController
{
#[Route('/quotation', name: 'app_quotation_index', methods: ['GET', 'POST'])]
public function index(Request $request, QuotationRepository $quotationRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response 
{
    $invoiceItem = new InvoiceItem();
    $query = $quotationRepository->createQueryBuilder('a')->getQuery();

    $data = $paginatorInterface->paginate(
        $query,
        $request->query->getInt('page', 1),
        15
    );

    // Initialiser $productDataByInvoiceId avant de l'utiliser
    $productDataByQuotationId = [];

    foreach ($data as $quotation) {
        $quotationId = $quotation->getId();
        $invoiceItems = $quotation->getInvoiceItems();
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

        $productDataByQuotationId[$quotationId] = $productData;
    }

    return $this->render('invoice/page_invoice_index.html.twig', [
        'data' => $data,
        'invoiceItem' => $invoiceItem,
        'products' => $productDataByQuotationId,
        'pathEdit' => 'app_quotation_edit',
        'pathDelete' => 'app_quotation_delete',
        'type' => 'quotation',
    ]);
}


    #[Route('quotation/new', name: 'app_quotation_new', methods: ['GET', 'POST'])]
    public function new(ProductRepository $productRepository, Request $request): Response
    {
        $quotation = new Quotation();
        /* $form = $this->createForm(SelectFormType::class, $quotation);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitez l'enregistrement de l'entité $quotation comme d'habitude
        } */
        
        $invoiceItem = new InvoiceItem();
        $products = $productRepository->findAll();

        // Pour créer une notification de succès
        $this->addFlash('success', 'La facture a été créée avec succès');
        
        return $this->render('invoice/page_invoice_new.html.twig', [
            'quotation' => $quotation,
            'invoiceItem' => $invoiceItem,
            'products' => $products,
            'type' => 'quotation',
        ]);
    }

    #[Route('quotation/edit/{id}', name: 'app_quotation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quotation $quotation, EntityManagerInterface $entityManager): Response
    {
        return $this->render('invoice/page_invoice_new.html.twig', [
            'quotation' => $quotation,
            'edit' => "edit",
        ]);
    }


    #[Route('quotation/delete/{id}/{token}', name: 'app_quotation_delete', methods: ['GET'])]
    public function delete(Quotation $quotation, string $token, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quotation->getId(), $token)) {
            $entityManager->remove($quotation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quotation_index', [], Response::HTTP_SEE_OTHER);
    }
}