<?php

namespace App\Model;

use Nette;
use Nette\Utils\DateTime;
use Nette\Templating\FileTemplate;
use \Joseki\Application\Responses\PdfResponse;

class PDFOutputController
{
    public function viewPDF(array $order, array $orderDetails): void
    {   
        date_default_timezone_set('Europe/Prague');
        $date = date("d. m. Y");
        $datePlusMonth = date("d. m. Y", strtotime("+30 days"));

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . "/../templates/Pdf/pdf.latte");
        $template->order = $order;
        // Tip: In template to make a new page use <pagebreak>

        $pdf = new \Joseki\Application\Responses\PdfResponse($template);

        // optional
        $pdf->documentTitle = date("Y-m-d") . " My super title"; // creates filename 2012-06-30-my-super-title.pdf
        $pdf->pageFormat = "A4-L"; // wide format
        $pdf->getMPDF()->setFooter("|© www.mysite.com|"); // footer
        
        // do something with $pdf
        $this->sendResponse($pdf);

        

      
    }
}