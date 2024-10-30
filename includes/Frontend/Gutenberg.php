<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Frontend;

use CCFBW_Blocks_Integration;
use WC_Blocks_Utils;
use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;

class Gutenberg {
	public function __construct() {
		add_action( 'woocommerce_blocks_loaded', array( $this, 'load_block' ) );

		add_action( 'before_woocommerce_init', array( $this, 'compatibility_custom_order_tables' ) );

		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'update_block_order_meta' ), 10, 2 );

		add_action( 'woocommerce_init', array( $this, 'api_init' ) );
	}

	public function api_init() {
		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CheckoutSchema::IDENTIFIER,
				'namespace'       => 'ccfbw-checkout-data',
				'data_callback'   => array( $this, 'data_callback' ),
				'schema_callback' => array( $this, 'schema_callback' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	public function data_callback(): array {
		return array(
			'contact-information' => '',
			'shipping-address'    => '',
			'shipping-methods'    => '',
			'billing-address'     => '',
		);
	}

	public function schema_callback(): array {
		return array(
			'contact-information' => array(
				'description' => __( 'Contact Information Fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'        => array( 'object', 'null' ),
				'readonly'    => true,
			),
			'shipping-address'    => array(
				'description' => __( 'Shipping Address Fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'        => array( 'object', 'null' ),
				'readonly'    => true,
			),
			'shipping-methods'    => array(
				'description' => __( 'Shipping Methods Fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'        => array( 'object', 'null' ),
				'readonly'    => true,
			),
			'billing-address'     => array(
				'description' => __( 'Billing Address Fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'        => array( 'object', 'null' ),
				'readonly'    => true,
			),
		);
	}

	public function update_block_order_meta( \WC_Order $order, $request ) {
		$extensions = $request['extensions'] ?? '';

		if ( isset( $extensions['ccfbw-checkout-data'] ) ) {
			$save     = false;
			$sections = $extensions['ccfbw-checkout-data'];

			foreach ( $sections as $fields ) {
				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field_key => $field_value ) {
						if ( ! empty( $field_value ) ) {
							$order->update_meta_data( $field_key, $field_value );
						}

						$save = true;
					}
				}
			}

			if ( $save ) {
				$order->save();
			}
		}
	}

	public function load_block() {
		if ( ( $this->blocks_active() ) && ( $this->blocks_version_supported() ) ) {

			if ( WC_Blocks_Utils::has_block_in_page( wc_get_page_id( 'checkout' ), 'woocommerce/checkout' ) ) {
				register_block_type_from_metadata( CCFBW_PATH . '/build/js/ccfbw-contact-information-block' );
				register_block_type_from_metadata( CCFBW_PATH . '/build/js/ccfbw-billing-address-block' );
				register_block_type_from_metadata( CCFBW_PATH . '/build/js/ccfbw-shipping-address-block' );
				register_block_type_from_metadata( CCFBW_PATH . '/build/js/ccfbw-shipping-methods-block' );

				require_once CCFBW_PATH . '/includes/Frontend/Blocks/CCFBW_Blocks_Integration.php';

				add_action( 'woocommerce_blocks_checkout_block_registration', array( $this, 'register_block' ), 10, 1 );
			}
		}
	}

	public function register_block( $integration_registry ) {
		$integration_registry->register( new CCFBW_Blocks_Integration() );
	}

	/**
	 * Declarate compatibility with WooCommerce Custom Order Tables.
	 */
	public static function compatibility_custom_order_tables() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', CCFBW_FILE, true );
		}
	}

	public function blocks_active() {
		return class_exists( 'Automattic\WooCommerce\Blocks\Package' );
	}

	public function blocks_version_supported() {
		return version_compare(
			\Automattic\WooCommerce\Blocks\Package::get_version(),
			'7.3.0',
			'>='
		);
	}
}
