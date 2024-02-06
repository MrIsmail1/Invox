<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Repository\QuotationRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DashboardController extends AbstractController
{

    #[Route('/', name: 'dashboard',methods: ['GET','POST'])]
    public function dashboard(InvoiceRepository $invoiceRepository, QuotationRepository $quotationRepository, CustomerRepository $customerRepository,Request $request): Response
    {

/* -------------------------------------- SelectForm -------------------------------------- */      
        $form = $this->createFormBuilder()
        ->add('mois', ChoiceType::class, [
            'choices' => [
                'Janvier' => 1,
                'Février' => 2,
                'Mars' => 3,
                'Avril' => 4,
                'Mai' => 5,
                'Juin' => 6,
                'Juillet' => 7,
                'Août' => 8,
                'Septembre' => 9,
                'Octobre' => 10,
                'Novembre' => 11,
                'Décembre' => 12,
            ],
            'label' => 'Mois',
        ])
        ->add('annee', ChoiceType::class, [
            'choices' => [
                '2024' => 2024,
                '2023' => 2023,
            ],
            'label' => 'Année',
        ])
        ->add('filtrer', SubmitType::class, ['label' => 'Filtrer'])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $mois = $data['mois'];
            $annee = $data['annee'];
            $dateString = $annee . '-' . $mois . '-05 18:13:42';
            
/* -------------------------------------- Recup Data -------------------------------------- */  

            /* $realInvoiceCount = array_filter($invoiceRepository, function($invoice) {
                return $invoice->isIsValid() === true;
            });
            dd($realInvoiceCount); */

            $filteredQuotations = $quotationRepository->findByCreatedAt(new \DateTimeImmutable($dateString));
            $quotationCount = count($filteredQuotations);
            $quotationValid = $quotationRepository->countByIsValid(true);
             $validQuotation = array_filter($filteredQuotations, function($quotation) {
                return $quotation->isIsValid() === true;
            });
            $quotationValid = count($validQuotation);

            $filteredInvoices = $invoiceRepository->findByCreatedAt(new \DateTimeImmutable($dateString));
            $invoiceCount = count($filteredInvoices);
            $validInvoices = array_filter($filteredInvoices, function($invoice) {
                return $invoice->isIsValid() === true;
            });
            $invoiceValid = count($validInvoices);         
}
/* -------------------------------------- Render -------------------------------------- */
            // Passez les données filtrées au modèle pour l'affichage
              return $this->render('dashboard/page_dashboard.html.twig', [
                'quotation' => [
                    'count' => $quotationCount ?? 20,
                    'valid' => $quotationValid ?? true,
                    'filtered' => $filteredQuotations ?? [],
                ],
                'invoice' => [
                    'count' => $invoiceCount ?? 10,
                    'valid' => $invoiceValid ?? true,
                    'createdAt' => $filteredInvoices ?? [],
                ],
                'form' => $form->createView(),
            ]);
        
    }
}