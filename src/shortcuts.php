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

    $shop_name  = "{$data['name']}";
    $orders     = $data['orders'];

    $options = new Options();
    $options->set('defaultFont', 'sans-serif')
        ->set('isHtml5ParserEnabled', true)
        ->set('isFontSubsettingEnabled', true)
        ->set('isPhpEnabled', true)
        ->set('tempDir', INVOICES_DIR);

    if (count($orders) > 1)
    {
        $name = "Orders - {$shop_name}";
    }
    else
    {
        $invoice    = $orders[0]['invoice_no']; // todo make isset to contrib
        $order_id   = $orders[0]['order_id'];
        $name       = "{$invoice}_{$shop_name}";
    }

    $pdf = new Dompdf($options);
    $pdf->loadHtml($view);
    $pdf->render();
    $pdf->stream("{$name}.pdf");

    // todo: saving as file
    // $pdf->output();
    // file_put_contents($_SERVER['DOCUMENT_ROOT']."/tmp/".$name, $output);
    // and redirect to the file
}