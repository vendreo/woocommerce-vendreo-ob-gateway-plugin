<?php
/*
 * Plugin Name: WooCommerce Vendreo Payment Gateway
 * Plugin URI: https://docs.vendreo.com
 * Description: Take Vendreo payments on your store.
 * Author: Vendreo
 * Author URI: https://app.vendreo.com
 * Version: 1.0.0
 */

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'vendreo_add_gateway_class' );
function vendreo_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Vendreo_Gateway'; // your class name is here
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'vendreo_init_gateway_class' );


function vendreo_init_gateway_class() {

    class WC_Vendreo_Gateway extends WC_Payment_Gateway {

        /**
         * Class constructor, more about it in Step 3
         */
        public function __construct() {

            $this->id = 'vendreo'; // payment gateway plugin ID
            $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'Vendreo Gateway';
            $this->method_description = 'Description of Vendreo payment gateway'; // will be displayed on the options page

            // gateways can support subscriptions, refunds, saved payment methods,
            // but in this tutorial we begin with simple payments
            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->application_key = $this->testmode ? $this->get_option( 'test_application_key' ) : $this->get_option( 'application_key' );
            $this->secret_key = $this->testmode ? $this->get_option( 'test_secret_key' ) : $this->get_option( 'secret_key' );

            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            //Callback API Handle

            add_action( 'woocommerce_api_wc_vendreo_gateway', array( $this, 'callback_handler' ) );


            //add_action( 'woocommerce_thankyou_order_received_text', array($this, 'thankyou_page') );


            //add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'check_response'  ));

            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

            //add_action( 'woocommerce_api_vendreo', array( $this, 'webhook'));
            //add_action( 'woocommerce_api_wc_vendreo', array( $this, 'webhook' ) );


        }

        /**
         * Plugin options, we deal with it in Step 3 too
         */
        public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Vendreo Gateway',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Vendreo',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_application_key' => array(
                    'title'       => 'Test Application Key',
                    'type'        => 'text'
                ),
                'test_secret_key' => array(
                    'title'       => 'Test Secret Key',
                    'type'        => 'password',
                ),
                'application_key' => array(
                    'title'       => 'Live Application Key',
                    'type'        => 'text'
                ),
                'secret_key' => array(
                    'title'       => 'Live Secret Key',
                    'type'        => 'password'
                )
            );

        }

        /**
         * You will need it if you want your custom credit card form, Step 4 is about it
         */
        public function payment_fields() {

            //...

        }

        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts() {

            //...

        }


        /*
          * Fields validation, more in Step 5
         */
        public function validate_fields() {

            //...

        }

        /*
         * We're processing the payments here, everything about it is in Step 5
         */
        public function process_payment( $order_id ) {
            global $woocommerce;


            // we need it to get any order detailes
            $order = wc_get_order( $order_id );

            // Mark as on-hold (we're awaiting the payment)
            $order->update_status( 'pending-payment', __( 'Awaiting Vendreo Payment', 'wc-gateway-vendreo' ) );

            // Remove cart
            WC()->cart->empty_cart();

            $post = [
                'application_key' => $this->application_key,
                'amount' => ($order->get_total() * 100),
                'country_code' => 'GB',
                'currency' => 'GBP',
                "description" => "Product Description",
                'payment_type' => 'single',
                "redirect_url" => $this->get_return_url($order),
                "reference_id" => $order_id,
            ];

            header('Content-Type: application/json'); // Specify the type of data
            $ch = curl_init('https://api.vendreo.com/v1/request-payment'); // Initialise cURL
            $post = json_encode($post); // Encode the data array into a JSON string
            $authorization = "Authorization: Bearer ".$this->secret_key; // Prepare the authorisation token
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post); // Set the posted fields
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch); // Execute the cURL statement
            curl_close($ch); // Close the cURL connection

            $result = json_decode($result);

            return array(
                'result'    => 'success',
                'redirect'  => $result->redirect_url
            );



        }


        public function callback_handler()
        {
            $json = file_get_contents('php://input');
            $data = json_decode($json);

            $order = wc_get_order($data->reference_id);
            if ($data->act == 'payment_completed')
            {
                $order->payment_complete();
                wc_reduce_stock_levels($order->get_id());
            }

        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        /*public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

            if ( $this->instructions && ! $sent_to_admin && 'offline' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
        }*/


        public function check_response()
        {

        }
        /*
         * In case you need a webhook, like PayPal IPN etc
         *
         */
        public function webhook() {

           // $order = wc_get_order(wc_get_order_id_by_order_key( $_GET['key']));

            echo "TEST";
            //$order->update_status( 'on-hold', __( 'Awaiting Vendreo Payment Confirmation', 'wc-gateway-vendreo' ) );

            //var_dump($order);

            //ray($order);
            //$order->payment_complete();
            //$order->reduce_order_stock();

            //update_option('webhook_debug', $_GET);
//
        }
    }

    function at_rest_testing_endpoint()
    {

        global $woocommerce;

        $order = wc_get_order(wc_get_order_id_by_order_key($_GET['key']));
        $order->update_status( 'on-hold', __( 'Awaiting Vendreo Payment Confirmation', 'wc-gateway-vendreo' ) );

        return wp_redirect($order->get_checkout_order_received_url());

        //return new WP_REST_Response('Howdy!!');
    }

    /**
     * at_rest_init
     */
    function at_rest_init()
    {
        // route url: domain.com/wp-json/$namespace/$route
        $namespace = 'vendreo/v1';
        $route     = 'postback';

        register_rest_route($namespace, $route, array(
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => 'at_rest_testing_endpoint'
        ));
    }

    add_action('rest_api_init', 'at_rest_init');
}