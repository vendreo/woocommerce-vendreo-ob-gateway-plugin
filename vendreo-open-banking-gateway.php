<?php
/*
 * Plugin Name:       Vendreo Open Banking Gateway
 * Plugin URI:        https://github.com/vendreo/woocommerce-vendreo-ob-gateway-plugin
 * Description:       Accept bank transfer payments using Vendreo's Payment Gateway.
 * Version:           2.0.0
 * Requires at least: 6.1.1
 * Requires PHP:      7.2
 * Author:            Vendreo
 * Author URI:        https://docs.vendreo.com/
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       vendreo-open-banking-gateway
 * Domain Path:       /languages
*/

use Automattic\WooCommerce\Utilities\FeaturesUtil;

define( 'VENDREO_OB_PLUGIN_DIR_PATH', plugins_url( '', __FILE__ ) );

add_action( 'plugins_loaded', 'vendreo_ob_woocommerce_plugin', 0 );

function vendreo_ob_woocommerce_plugin() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	include plugin_dir_path( __FILE__ ) . 'includes/php/class-woocommerce-vendreo-ob-gateway.php';
}

add_filter( 'woocommerce_payment_gateways', 'vendreo_ob_add_woocommerce_gateway' );

function vendreo_ob_add_woocommerce_gateway( $gateways ) {
	$gateways[] = 'WooCommerce_Vendreo_OB_Gateway';
	return $gateways;
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 */
function vendreo_ob_declare_ob_cart_checkout_blocks_compatibility() {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
	}
}

add_action( 'before_woocommerce_init', 'vendreo_ob_declare_ob_cart_checkout_blocks_compatibility' );
add_action( 'woocommerce_blocks_loaded', 'vendreo_ob_register_order_approval_payment_method_type' );

/**
 * Custom function to register a payment method type
 */
function vendreo_ob_register_order_approval_payment_method_type() {
	if ( ! class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'includes/php/class-vendreo-ob-gateway-blocks.php';

	add_action(
		'woocommerce_blocks_payment_method_type_registration',
		function ( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
			$payment_method_registry->register( new Vendreo_OB_Gateway_Blocks() );
		}
	);
}
