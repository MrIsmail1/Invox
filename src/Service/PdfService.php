<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    private $domPdf;
    
    public function __construct() {
        $this->domPdf = new DomPdf();
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Times-Roman');
        $this->domPdf->setOptions($pdfOptions);
    }

    /* Pour afficher le pdf sous format pdf sans le télécharger */
    public function showPdfFile($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->setPaper('A4', 'portrait');
        $this->domPdf->render();
        $this->domPdf->stream("export.pdf", [
            'Attachment' => false
        ]);
        return $this->domPdf->output();
    }

    /* Pour télécharger le pdf */
    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        return $this->domPdf->output();
    }
    
}