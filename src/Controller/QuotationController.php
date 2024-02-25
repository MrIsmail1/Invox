<?php

namespace App\Controller;

use App\Entity\InvoiceItem;
use App\Entity\Quotation;
use App\Repository\ProductRepository;
use App\Repository\QuotationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SearchAutocomplete;

class QuotationController extends AbstractController
{
#[Route('/quotation', name: 'app_quotation_index', methods: ['GET', 'POST'])]
public function index(Request $request, QuotationRepository $quotationRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response 
{
    $invoiceItem = new InvoiceItem();

    $form = $this->createForm(SearchAutocomplete::class);
    $form->handleRequest($request);

    // Initialiser la requête de base pour toutes les quotations
    $queryBuilder = $quotationRepository->createQueryBuilder('a');

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
            'modal' => "invoiceModal",
            'products' => $productDataByQuotationId,
            'pathEdit' => 'app_quotation_edit',
            'pathDelete' => 'app_quotation_delete',
            'type' => 'quotation',
            'SearchBar' => $form->createView(),
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
        if ($this->isCsrfTokenValid('delete' . $quotation->getId(), $token)) {
            $entityManager->remove($quotation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quotation_index', [], Response::HTTP_SEE_OTHER);
    }
}