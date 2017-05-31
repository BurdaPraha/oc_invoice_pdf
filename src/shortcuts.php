<?php

use Dompdf\Dompdf;
use Dompdf\Options;

function invoice_pdf($data, $name) {

    $options = new Options();
    $options->set('defaultFont', 'Courier');

    if (count($name) > 1) {
        $name = "Orders";
    }else{
        $name = 'Order_'.$name[0]['order_id'];
    }

    $pdf = new Dompdf(); // $options
    $pdf->loadHtml($data);

    //$pdf->setPaper('A4', 'landscape');



    $pdf->render();
    $pdf->stream($name.".pdf");
}