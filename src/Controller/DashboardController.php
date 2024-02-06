<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Repository\QuotationRepository;
use App\Repository\CustomerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Form\DashboardFormType;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard',methods: ['GET','POST'])]
    public function dashboard(InvoiceRepository $invoiceRepository, QuotationRepository $quotationRepository, CustomerRepository $customerRepository,Request $request): Response

    {
         $form = $this->createForm(DashboardFormType::class, null, [
            'method' => 'POST'
        ]);

        $form->handleRequest($request);

        // Variables initiales
        $invoice = [];
        $quotation = [];
        $customer = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $startDate = $data['start_date'];
            $endDate = $data['end_date'];

            // Assurez-vous que les dates sont au format approprié pour votre DB
            $invoice = $invoiceRepository->findByCreatedAtRange($startDate, $endDate);
            $quotation = $quotationRepository->findByCreatedAtRange($startDate, $endDate);
            $customer = $customerRepository->findByCreatedAtRange($startDate, $endDate);
        } else {
            $invoice = $invoiceRepository->findAll();
            $quotation = $quotationRepository->findAll();
            $customer = $customerRepository->findAll();
        }

        // Comptages et récupération des totaux mensuels
        $numberOfValidQuotations = $quotationRepository->countValidQuotations();
        $numberOfPaidInvoices = $invoiceRepository->countInvoicesByStatus("Payé");
        $numberOfLateInvoices = $invoiceRepository->countInvoicesByStatus("Retard");

        // Extraction des totaux par mois pour les 12 derniers mois
        $totalsByMonth = $invoiceRepository->getTotalByMonthForLastYear();

        // Préparation des données pour le graphique
        $months = [];
        $totals = [];
        foreach ($totalsByMonth as $monthlyTotal) {
            $monthName = date("F", mktime(0, 0, 0, $monthlyTotal['month'], 10)); // Convertit le numéro du mois en nom
            $months[] = $monthName;
            $totals[] = $monthlyTotal['total'];
        }

        return $this->render('dashboard/page_dashboard.html.twig', [
            'DashboardForm' => $form->createView(),
            'invoice' => $invoice,
            'quotation' => $quotation,
            'customer' => $customer,
            'numberOfInvoices' => count($invoice),
            'numberOfCustomers' => count($customer),
            'numberOfQuotations' => count($quotation),
            'numberOfValidQuotations' => $numberOfValidQuotations,
            'numberOfPaidInvoices' => $numberOfPaidInvoices,
            'numberOfLateInvoices' => $numberOfLateInvoices,
            'months' => $months,
            'totals' => $totals,
        ]);
    }
}