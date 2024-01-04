<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\InvoiceItem;
use App\Entity\Service;
use App\Entity\User;
use App\Repository\ServiceRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UploadFile;
use App\Form\InvoiceType;
use App\Form\SearchAutocomplete;
use Knp\Component\Pager\PaginatorInterface;

class InvoiceController extends AbstractController
{
    #[Route('/invoice', name: 'invoice_index', methods: ['GET', 'POST'])]
    public function index(Request $request, InvoiceRepository $invoiceRepository, EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface): Response 
    {

    $query = $invoiceRepository->createQueryBuilder('a')
    ->getQuery();

    $pagination = $paginatorInterface->paginate(
        $query,
        $request->query->getInt('page', 1),
        15
    );
/* --------------------------------------- FORM ------------------------------------------ */

        $user = new User();

        $form = $this->createForm(UploadFile::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/page_invoice_index.html.twig', [
            'form' => $form,
            'pagination' => $pagination,
        ]);
}

    #[Route('invoice/new', name: 'invoice_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,ServiceRepository $serviceRepository): Response
    {
        $invoice = new Invoice();
        $invoiceItem = new InvoiceItem();

        $services = $serviceRepository->findAll();

        return $this->render('invoice/page_invoice_new.html.twig', [
            'invoice' => $invoice,
            'invoiceItem' => $invoiceItem,
            'services' => $services,
        ]);
    }

    #[Route('invoice/{id}', name: 'invoice_show', methods: ['GET'])]
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    #[Route('invoice/edit/{id}', name: 'invoice_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('invoice/page_invoice_edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    #[Route('invoice/delete/{id}/{token}', name: 'invoice_delete', methods: ['GET'])]
    public function delete(Invoice $invoice, string $token, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $token)) {
            $entityManager->remove($invoice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}