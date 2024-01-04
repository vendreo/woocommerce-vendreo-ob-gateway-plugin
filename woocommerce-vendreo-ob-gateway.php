<?php
/*
Plugin Name: WooCommerce Vendreo Gateway (Open Banking)
Description: Accept bank transfer payments using Vendreo's Payment Gateway.
Version: 1.2.0
Author: Vendreo
Author URI: docs.vendreo.com
License: MIT
License URI: https://opensource.org/license/mit/
Text Domain: woocommerce-vendreo-ob-gateway
Domain Path: /languages
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;

define( 'VENDREO_OB_PLUGIN_DIR_PATH', plugins_url( '', __FILE__ ) );

add_action('plugins_loaded', 'woocommerce_vendreo_ob_plugin', 0);

function woocommerce_vendreo_ob_plugin()
{
    if (!class_exists('WC_Payment_Gateway'))
        return;

    include(plugin_dir_path(__FILE__) . 'includes/php/woocommerce-vendreo-ob-gateway.php');
}

add_filter('woocommerce_payment_gateways', 'add_woocommerce_vendreo_ob_gateway');

function add_woocommerce_vendreo_ob_gateway($gateways)
{
    $gateways[] = 'WooCommerce_Vendreo_OB_Gateway';
    return $gateways;
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 */
function declare_ob_cart_checkout_blocks_compatibility()
{
    if (class_exists(FeaturesUtil::class)) {
        FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}

add_action('before_woocommerce_init', 'declare_ob_cart_checkout_blocks_compatibility');
add_action('woocommerce_blocks_loaded', 'vendreo_ob_register_order_approval_payment_method_type');

/**
 * Custom function to register a payment method type
 */
function vendreo_ob_register_order_approval_payment_method_type()
{
    if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }

    require_once plugin_dir_path(__FILE__) . 'includes/php/vendreo-ob-block.php';

    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
            $payment_method_registry->register(new Vendreo_OB_Gateway_Blocks);
        }
    );
}
