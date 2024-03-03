<?php

namespace App\Controller;

use App\Form\DashboardFormType;
use App\Repository\CustomerRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuotationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard', methods: ['GET', 'POST'])]
    public function dashboard(InvoiceRepository $invoiceRepository, QuotationRepository $quotationRepository, CustomerRepository $customerRepository, Request $request, SessionInterface $session): Response
    {

        $user = $this->getUser();
        if ($user && !$user->isIsVerified()) {
            return $this->redirectToRoute('app_logout', ['alert' => true]);
        }
        $theme = $user->getTheme();
        $session->set('theme', $theme->getValue());
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
            $invoice = $invoiceRepository->findByUserAndCreatedAtRange($user, $startDate, $endDate);
            $quotation = $quotationRepository->findByUserAndCreatedAtRange($user, $startDate, $endDate);
            /* $customer = $customerRepository->findByUserAndCreatedAtRange($user, $startDate, $endDate); */
        } else {
            $invoice = $invoiceRepository->findByUser($user);
            $quotation = $quotationRepository->findByUser($user);
            $customer = $user->getCustomers();
        }

        // Comptages et récupération des totaux mensuels
        $numberOfValidQuotations = $quotationRepository->countValidQuotationsByUser($user);
        $numberOfPaidInvoices = $invoiceRepository->countInvoicesByStatusAndUser("Payé", $user);
        $numberOfLateInvoices = $invoiceRepository->countInvoicesByStatusAndUser("Retard", $user);

        // Extraction des totaux par mois pour les 12 derniers mois
        $totalsByMonth = $invoiceRepository->getTotalByMonthForLastYearForUser($user);

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
            'user' => $user,
        ]);
    }
}