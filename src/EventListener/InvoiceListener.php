<?php

namespace App\EventListener;

use App\Entity\Invoice;
use App\Service\PdfService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;


#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Invoice::class)]
class InvoiceListener
{
    private MailerInterface $mailer;
    private PdfService $pdfService;
    private Security $security;
    private EntityManagerInterface $entityManager;

    private Environment $twig;


    public function __construct(MailerInterface $mailer, PdfService $pdfService, Security $security, EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->pdfService = $pdfService;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    public function postPersist(Invoice $invoice, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Invoice) {
            return;
        }

        $customer = $entity->getCustomer();
        $user = $this->security->getUser();
        if ($customer) {
            $customerEmail = $customer->getEmail();
            $htmlContent = $this->twig->render('invoice/template_invoice.html.twig', [
                'user' => $user,
            ]);
            // Generate the PDF from the HTML content
            $pdfContent = $this->generatePdfInvoice($entity);

            // Create the email and attach the generated PDF
            $email = (new Email())
                ->from('your_email@example.com')
                ->to($customerEmail)
                ->subject('Invox - Confirmation de la création de votre facture n°' . $entity->getId())
                ->html($htmlContent)
                ->attach($pdfContent, 'invoice.pdf', 'application/pdf');

            // Send the email
            $this->mailer->send($email);
        }
    }


    public function generatePdfInvoice(Invoice $invoice)
    {
        if (!$invoice) {
            throw $this->createNotFoundException('La facture demandée n\'existe pas');
        }

        $user = $this->security->getUser();
        $companyDetails = $user->getCompanyDetails();
        $this->entityManager->initializeObject($companyDetails);

        $customer = $invoice->getCustomer();
        $this->entityManager->initializeObject($customer);

        // Récupérer les données nécessaires pour cette invoice spécifique
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
                    'category' => $product->getCategory() ? $product->getName() : '',
                    'quantity' => $quantity,
                    'discount' => $discount,
                ];
            }
        }

        $html = $this->twig->render('invoice/page_invoice_pdf.html.twig', [
            'products' => $productData,
            'data' => $invoice,
            'companyDetails' => $companyDetails,
            'customer' => $customer,
        ]);

        // Generate PDF
        return $this->pdfService->generateBinaryPDF($html);

    }
}