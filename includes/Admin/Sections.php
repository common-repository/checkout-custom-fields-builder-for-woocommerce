<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

defined( 'ABSPATH' ) || exit;

class Sections {
	public function __construct() {
		$_sections = apply_filters( 'ccfbw_get_sections', array() );

		if ( ! empty( $_sections ) ) {
			foreach ( $_sections as $section ) {
				if ( method_exists( self::class, $section['id'] ) ) {
					add_action( 'ccfbw_checkout_sections', array( $this, $section['id'] ), $section['priority'] );
				}
			}
		}
	}

	protected static function get_sections_for_default() {
		$_sections = array(
			self::add(
				array(
					'id'       => 'billing',
					'title'    => __( 'Billing Details', 'checkout-custom-fields-builder-for-woocommerce' ),
					'priority' => 10,
				)
			),
			self::add(
				array(
					'id'       => 'shipping',
					'title'    => __( 'Shipping Details', 'checkout-custom-fields-builder-for-woocommerce' ),
					'priority' => 20,
				)
			),
		);

		$account_fields = Fields::get_woo_fields( 'account' );
		if ( ! empty( $account_fields ) ) {
			$_sections[] = self::add(
				array(
					'id'       => 'account',
					'title'    => __( 'Account Details', 'checkout-custom-fields-builder-for-woocommerce' ),
					'priority' => 30,
				)
			);
		}

		$_sections[] = self::add(
			array(
				'id'       => 'order',
				'title'    => __( 'Order Details', 'checkout-custom-fields-builder-for-woocommerce' ),
				'priority' => 40,
			)
		);

		return wp_list_sort( $_sections, array( 'priority' => 'ASC' ) );
	}

	protected static function get_sections_for_gutenberg() {
		return array(
			self::add(
				array(
					'id'       => 'checkout-form',
					'title'    => __( 'Form', 'checkout-custom-fields-builder-for-woocommerce' ),
					'priority' => 10,
				)
			),
		);
	}

	public static function get_woo_sections(): array {
		$editor = apply_filters( 'ccfbw_current_editor', false );

		if ( 'elementor' === $editor ) {
			$_sections = self::get_sections_for_default();
		} else {
			$_sections = self::get_sections_for_gutenberg();
		}

		return $_sections;
	}

	public static function add( $args ): array {
		$default_args = array(
			'id'       => $args['id'],
			'title'    => $args['title'],
			'fields'   => Fields::get_woo_fields( $args['id'] ),
			'default'  => true, // Indication that the section comes by default from Woocommerce
			'opened'   => true,
			'priority' => 10,
		);

		return wp_parse_args( $args, $default_args );
	}

	public function billing() {
		do_action( 'woocommerce_checkout_billing' );
	}

	public function shipping() {
		do_action( 'woocommerce_checkout_shipping' );
	}

	public function order() {
		do_action( 'ccfbw_checkout_order_details' );
	}

	public function account() {
		do_action( 'ccfbw_checkout_account_details' );
	}
}
