<?php

class ControllerModuleInvoicePdf extends Controller {

    protected $order_id;

    public function __construct($registry)
    {
        // set order ID
        if(isset($_GET['order_id']))
        {
            $this->order_id = (int) $_GET['order_id'];
        }
        else
        {
            throw new Exception("Order ID missing");
        }

        parent::__construct($registry);
    }


    public function index()
    {
        $this->load->model('sale/order');
        $this->load->model('setting/setting');

        $invoice_request = $this->model_sale_order->createInvoiceNo($this->order_id);
        $invoice_response = $invoice_request;

        if(!empty($invoice_response)) {

            $custom_parameters = [
                'return_invoice_path' => true,
                'orders' => [
                    $this->order_id
                ]
            ];

            $controller_request = $this->load->controller('sale/order/invoice', $custom_parameters);
            $json = json_encode(['path' => $controller_request]);
        }
        else {
            $json = json_encode(['error' => 'createInvoiceNo error']);
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($json);
    }
}