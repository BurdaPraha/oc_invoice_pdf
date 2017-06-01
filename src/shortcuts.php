<?php

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Helper for printing invoices
 * @param $view
 * @param $data
 */
function invoice_pdf($view, $data) {

    /*
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    */

    $shop_name  = "{$data['name']}";
    $orders     = $data['orders'];

    $options = new Options();
    $options->set('defaultFont', 'serif')
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
}