<?php


class ControllerModuleInvoicePdf extends Controller {
    private $error = array();

    protected $uniModul;
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
        $invoice_no = $this->model_sale_order->createInvoiceNo($this->order_id);

        $order_data = $this->getOrderData($this->order_id);
        $order_view = $this->load->view('sale/order_invoice', $order_data);
        $pdf_printer = invoice_pdf($order_view, $order_data, true);

        $response = json_encode(['invoice' => $pdf_printer]);


        return $response;
    }

    private function getOrderData($order_id) {

        $order_info = $this->model_sale_order->getOrder($order_id);

        if ($order_info) {
            $store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

            if ($store_info) {
                $store_address = $store_info['config_address'];
                $store_email = $store_info['config_email'];
                $store_telephone = $store_info['config_telephone'];
                $store_fax = $store_info['config_fax'];
            } else {
                $store_address = $this->config->get('config_address');
                $store_email = $this->config->get('config_email');
                $store_telephone = $this->config->get('config_telephone');
                $store_fax = $this->config->get('config_fax');
            }

            if ($order_info['invoice_no']) {
                $invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
            } else {
                $invoice_no = '';
            }

            if ($order_info['payment_address_format']) {
                $format = $order_info['payment_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['payment_firstname'],
                'lastname'  => $order_info['payment_lastname'],
                'company'   => $order_info['payment_company'],
                'address_1' => $order_info['payment_address_1'],
                'address_2' => $order_info['payment_address_2'],
                'city'      => $order_info['payment_city'],
                'postcode'  => $order_info['payment_postcode'],
                'zone'      => $order_info['payment_zone'],
                'zone_code' => $order_info['payment_zone_code'],
                'country'   => $order_info['payment_country']
            );

            $payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            if ($order_info['shipping_address_format']) {
                $format = $order_info['shipping_address_format'];
            } else {
                $format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
            }

            $find = array(
                '{firstname}',
                '{lastname}',
                '{company}',
                '{address_1}',
                '{address_2}',
                '{city}',
                '{postcode}',
                '{zone}',
                '{zone_code}',
                '{country}'
            );

            $replace = array(
                'firstname' => $order_info['shipping_firstname'],
                'lastname'  => $order_info['shipping_lastname'],
                'company'   => $order_info['shipping_company'],
                'address_1' => $order_info['shipping_address_1'],
                'address_2' => $order_info['shipping_address_2'],
                'city'      => $order_info['shipping_city'],
                'postcode'  => $order_info['shipping_postcode'],
                'zone'      => $order_info['shipping_zone'],
                'zone_code' => $order_info['shipping_zone_code'],
                'country'   => $order_info['shipping_country']
            );

            $shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

            $this->load->model('tool/upload');

            $product_data = array();

            $products = $this->model_sale_order->getOrderProducts($order_id);

            foreach ($products as $product) {
                $option_data = array();

                $options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['value'];
                    } else {
                        $upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

                        if ($upload_info) {
                            $value = $upload_info['name'];
                        } else {
                            $value = '';
                        }
                    }

                    $option_data[] = array(
                        'name'  => $option['name'],
                        'value' => $value
                    );
                }

                $product_data[] = array(
                    'name'     => $product['name'],
                    'model'    => $product['model'],
                    'option'   => $option_data,
                    'quantity' => $product['quantity'],
                    'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                );
            }

            $voucher_data = array();

            $vouchers = $this->model_sale_order->getOrderVouchers($order_id);

            foreach ($vouchers as $voucher) {
                $voucher_data[] = array(
                    'description' => $voucher['description'],
                    'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
                );
            }

            $total_data = array();

            $totals = $this->model_sale_order->getOrderTotals($order_id);

            foreach ($totals as $total) {
                $total_data[] = array(
                    'title' => $total['title'],
                    'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
                );
            }

            $data['orders'][] = array(
                'order_id'	       => $order_id,
                'invoice_no'       => $invoice_no,
                'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
                'store_name'       => $order_info['store_name'],
                'store_url'        => rtrim($order_info['store_url'], '/'),
                'store_address'    => nl2br($store_address),
                'store_email'      => $store_email,
                'store_telephone'  => $store_telephone,
                'store_fax'        => $store_fax,
                'email'            => $order_info['email'],
                'telephone'        => $order_info['telephone'],
                'shipping_address' => $shipping_address,
                'shipping_method'  => $order_info['shipping_method'],
                'payment_address'  => $payment_address,
                'payment_method'   => $order_info['payment_method'],
                'product'          => $product_data,
                'voucher'          => $voucher_data,
                'total'            => $total_data,
                'comment'          => nl2br($order_info['comment'])
            );
        }

        return $data;
    }
}