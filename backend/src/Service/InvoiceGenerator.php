<?php

namespace App\Service;

use App\Entity\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class InvoiceGenerator
{
    public function __construct(
        private Environment $twig,
        private ParameterBagInterface $params
    ) {}

    public function generate(Order $order): string
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->twig->render('pdf/invoice.html.twig', [
            'order' => $order,
            'user'  => $order->getUser(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        $fileName = 'facture_' . $order->getId() . '_' . uniqid() . '.pdf';
        $invoiceDirectory = $this->params->get('kernel.project_dir') . '/var/invoices';

        if (!is_dir($invoiceDirectory) && !mkdir($invoiceDirectory, 0700, true) && !is_dir($invoiceDirectory)) {
            throw new \RuntimeException('Unable to create the private invoice directory.');
        }

        $filePath = $this->getFilePath($fileName);
        if (file_put_contents($filePath, $output, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write the invoice file.');
        }

        return $fileName;
    }

    public function getFilePath(string $fileName): string
    {
        if (preg_match('/\Afacture_\d+_[a-f0-9]+\.pdf\z/', $fileName) !== 1) {
            throw new \InvalidArgumentException('Invalid invoice filename.');
        }

        return $this->params->get('kernel.project_dir') . '/var/invoices/' . $fileName;
    }
}
