<?php


class ControllerModuleInvoicePdf extends Controller {

    private $error = array();
    protected $lowCaseBinderName;
    protected $configInfo;
    protected $order_id;

    public function __construct($registry)
    {
        global $config;

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


    public function index() {
        $this->load->model('sale/order');
        $this->load->model('setting/setting');

        $invoice_no = $this->model_sale_order->createInvoiceNo($this->order_id);

        if($invoice_no) {
            // todo
        }

        $custom_parameters = [
            'return_invoice_path' => true,
            'orders' => [
                $this->order_id
            ]
        ];

        $controller_response = $this->load->controller('sale/order/invoice', $custom_parameters);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode(['path' => $controller_response]));
    }
}