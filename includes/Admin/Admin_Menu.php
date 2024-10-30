<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

use CCFBW\Woocommerce\Checkout\Builder\Templates;

defined( 'ABSPATH' ) || exit;

class Admin_Menu {
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'admin_menu' ) );
	}

	public static function admin_menu() {
		add_submenu_page(
			'woocommerce',
			esc_html__( 'Checkout Builder', 'checkout-custom-fields-builder-for-woocommerce' ),
			esc_html__( 'Checkout Builder', 'checkout-custom-fields-builder-for-woocommerce' ),
			'manage_options',
			'ccfbw_settings',
			array( self::class, 'settings_page_view' )
		);
	}

	public static function settings_page_view() {
		wp_enqueue_style( 'ccfbw-settings' );
		wp_enqueue_script( 'ccfbw-settings' );

		do_action( 'ccfbw_admin_settings_page' );

		Templates::get_settings_template( 'main', true );
	}
}
