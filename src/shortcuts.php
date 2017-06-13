<?php

use Dompdf\Dompdf;
use Dompdf\Options;
use Svg\Surface;

/**
 * Helper for printing invoices
 * @param $view
 * @param $data
 */
function invoice_pdf($view, $data) {

    $storage    = INVOICES_DIR;
    $shop_name  = "{$data['name']}";
    $orders     = $data['orders'];

    if (count($orders) > 1)
    {
        $symbols = [];
        foreach($orders as $k => $o) {
            $symbols[] = $o['invoice_no'];
        }
        $symbols = implode("-", $symbols);

        $name = "Invoices_{$symbols}";
    }
    else
    {
        $invoice    = $orders[0]['invoice_no']; // todo make isset to contrib
        $order_id   = $orders[0]['order_id'];
        $name       = "{$invoice}_{$shop_name}";
    }

    $name       = "{$name}.pdf";
    $location   = "{$storage}/{$name}";

    //
    // check existing invoice in local storage
    //
    if(!file_exists($location))
    {
        $options = new Options();
        $options->set('defaultFont', 'sans-serif')
            ->set('isHtml5ParserEnabled', true)
            ->set('isFontSubsettingEnabled', true)
            ->set('isPhpEnabled', true)
            ->set('tempDir', $storage);

        $pdf = new Dompdf($options);
        $pdf->loadHtml($view);
        $pdf->render();

        // todo: check order status before saving
        file_put_contents($location, $pdf->output());

        //$pdf->render();
        //$pdf->stream("{$name}.pdf");
    }

    invoice_download($location);
}


/**
 * Response as file download
 * @param $location
 */
function invoice_download($location) {

    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename='" . basename($location) . "'");

    readfile ($location);
    exit();
}