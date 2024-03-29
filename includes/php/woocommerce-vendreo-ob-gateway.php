<?php

class WooCommerce_Vendreo_OB_Gateway extends WC_Payment_Gateway
{
    protected $url = 'https://api.vendreo.com/v1/request-payment';
    protected $testmode;
    protected $application_key;
    protected $secret_key;

    public function __construct()
    {
        $this->id = 'woocommerce_vendreo_ob_gateway';
        $this->method_title = __('WooCommerce Vendreo Gateway (Open Banking)', 'woocommerce-vendreo-ob-gateway');
        $this->title = 'Vendreo (Open Banking)';

        $this->method_description = __('Accept bank transfer payments using Vendreo\'s Payment Gateway.', 'woocommerce-vendreo-ob-gateway');
        $this->icon = 'https://cdn.vendreo.com/images/vendreo-fullcolour.svg';

        $this->supports = ['products'];

        $this->init_form_fields();
        $this->init_settings();

        $this->testmode = 'yes' === $this->get_option('testmode');
        $this->application_key = $this->testmode ? $this->get_option('test_application_key') : $this->get_option('application_key');
        $this->secret_key = $this->testmode ? $this->get_option('test_secret_key') : $this->get_option('secret_key');

        add_action('woocommerce_api_ob_callback', [$this, 'callback_handler']);
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    public function init_form_fields()
    {
        $this->form_fields = [
            'enabled' => [
                'title' => 'Enable/Disable',
                'label' => 'Enable Vendreo Open Banking Gateway',
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no'
            ],
            'title' => [
                'title' => 'Title',
                'type' => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default' => 'Vendreo Open Banking Payments',
                'desc_tip' => true,
            ],
            'description' => [
                'title' => 'Description',
                'type' => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default' => 'Pay directly from your banking app.',
            ],
            'testmode' => [
                'title' => 'Test mode',
                'label' => 'Enable Test Mode',
                'type' => 'checkbox',
                'description' => 'Place the payment gateway in test mode using test API keys.',
                'default' => 'yes',
                'desc_tip' => true,
            ],
            'test_application_key' => [
                'title' => 'Test Application Key',
                'type' => 'text'
            ],
            'test_secret_key' => [
                'title' => 'Test Secret Key',
                'type' => 'password',
            ],
            'application_key' => [
                'title' => 'Live Application Key',
                'type' => 'text'
            ],
            'secret_key' => [
                'title' => 'Live Secret Key',
                'type' => 'password'
            ],
        ];
    }

    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);

        $order->update_status('pending-payment', __('Awaiting Vendreo Open Banking Transfer Payment', 'woocommerce-vendreo-ob-gateway'));

        $post = [
            'application_key' => $this->application_key,
            'amount' => (int)($order->get_total() * 100),
            'country_code' => 'GB',
            'currency' => 'GBP',
            "description" => "Order #{$order_id}",
            'payment_type' => 'single',
            "redirect_url" => $this->get_return_url($order),
            'failed_url' => $this->get_cancelled_url(),
            "reference_id" => $order_id,
            "basket_items" => $this->get_basket_details(),
        ];

        header('Content-Type: application/json');
        $ch = curl_init($this->url);
        $authorization = "Authorization: Bearer " . $this->secret_key;
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseCode <> 200 || !$result) {
            return false;
        }

        $result = json_decode($result);

        return [
            'result' => 'success',
            'redirect' => $result->redirect_url
        ];
    }

    public function get_cancelled_url()
    {
        return wc_get_checkout_url();
    }

    /**
     * Returns itemised basket details.
     *
     * @return array[]
     */
    public function get_basket_details(): array
    {
        $basket = [];

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];

            $basket[] = [
                'description' => $product->get_name(),
                'quantity' => $cart_item['quantity'],
                'price' => (int)($product->get_price() * 100),
                'total' => (int)(($product->get_price() * 100) * $cart_item['quantity']),
            ];
        }

        return $basket;
    }

    public function callback_handler()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $orderId = explode('|', $data->reference_id)[0];

        $order = wc_get_order($orderId);

        if ($data->act === 'payment_completed')  {
            $order->payment_complete();
            wc_reduce_stock_levels($order->get_id());
        }

        if($data->act === 'payment_failed'){
            $order->update_status('failed', __('Vendreo Open Banking Payment Failed', 'woocommerce-vendreo-ob-gateway'));
        }
    }
}
