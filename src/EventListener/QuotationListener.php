<?php

namespace App\EventListener;

use App\Entity\Quotation;
use App\Service\PdfService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;


#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Quotation::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Quotation::class)]
class QuotationListener
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

    public function postPersist(Quotation $quotation, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Quotation) {
            return;
        }

        if ($entity->getStatus() !== 'Validé') {
            return;
        }
        $customer = $entity->getCustomer();
        $user = $this->security->getUser();
        if ($customer) {
            $customerEmail = $customer->getEmail();
            $htmlContent = $this->twig->render('invoice/template_quotation.html.twig', [
                'user' => $user,
            ]);
            // Generate the PDF from the HTML content
            $pdfContent = $this->generatePdfInvoice($entity);

            // Create the email and attach the generated PDF
            $email = (new Email())
                ->from('your_email@example.com')
                ->to($customerEmail)
                ->subject('Invox - Confirmation de la création de votre devis n°' . $entity->getId())
                ->html($htmlContent)
                ->attach($pdfContent, 'invoice.pdf', 'application/pdf');

            // Send the email
            $this->mailer->send($email);
        }
    }

    public function generatePdfInvoice(Quotation $quotation)
    {
        if (!$quotation) {
            throw $this->createNotFoundException('Le devis demandée n\'existe pas');
        }

        $user = $this->security->getUser();
        $companyDetails = $user->getCompanyDetails();
        $this->entityManager->initializeObject($companyDetails);

        $customer = $quotation->getCustomer();
        $this->entityManager->initializeObject($customer);

        // Récupérer les données nécessaires pour cette invoice spécifique
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
                    'category' => $product->getCategory() ? $product->getName() : '',
                    'quantity' => $quantity,
                    'discount' => $discount,
                ];
            }
        }

        $html = $this->twig->render('invoice/page_invoice_pdf.html.twig', [
            'products' => $productData,
            'data' => $quotation,
            'companyDetails' => $companyDetails,
            'customer' => $customer,
        ]);

        // Generate PDF
        return $this->pdfService->generateBinaryPDF($html);

    }

    public function postUpdate(Quotation $quotation, LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Quotation) {
            return;
        }

        if ($entity->getStatus() !== 'Validé') {
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
                ->subject('Invox - Confirmation de la modification de votre devis n°' . $entity->getId())
                ->html($htmlContent)
                ->attach($pdfContent, 'devis.pdf', 'application/pdf');

            // Send the email
            $this->mailer->send($email);
        }
    }
}