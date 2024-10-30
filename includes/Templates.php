<?php
namespace CCFBW\Woocommerce\Checkout\Builder;

use CCFBW\Woocommerce\Checkout\Builder\Admin\Options;

defined( 'ABSPATH' ) || exit;

class Templates {

	public static function locate_template( $template_name, $_args = array(), $path = '/templates/frontend/' ): string {
		$template_name = $path . $template_name . '.php';
		$template_name = apply_filters( 'ccfbw_template_name', $template_name, $_args );
		$_template     = apply_filters( 'ccfbw_template_file', CCFBW_PATH, $template_name ) . $template_name;

		return ( locate_template( $template_name ) ) ? locate_template( $template_name ) : $_template;
	}

	public static function load_template( $template_name, $_args = array(), $path = '/templates/frontend/' ) {
		ob_start();
		extract( $_args ); // phpcs:ignore

		$tpl = self::locate_template( $template_name, $_args, $path );

		if ( file_exists( $tpl ) ) {
			include $tpl;
		}

		return apply_filters( 'ccfbw_' . $template_name, ob_get_clean(), $_args );
	}

	public static function show_template( $template_name, $_args = array(), $path = '/templates/frontend/' ) {
		load_template( self::locate_template( $template_name, $_args, $path ), false, $_args );
	}

	public static function get_ccfbw_template( $template_name, $echo = false, $_args = array() ) {
		if ( ! $echo ) {
			return self::load_template( $template_name, $_args );
		}

		self::show_template( $template_name, $_args );
	}

	public static function get_settings_template( $template_name, $echo = false, $_args = array() ) {
		if ( ! $echo ) {
			return self::load_template( $template_name, $_args, '/templates/admin/' );
		}

		self::show_template( $template_name, $_args, '/templates/admin/' );
	}

	public static function load_admin_component( $path, $file_id, $args = array() ) {
		$script_id = sprintf( 'ccfbw-components-%s', esc_attr( $file_id ) );

		echo '<script type="text/template" id="' . esc_attr( $script_id ) . '">';
		self::get_settings_template( $path, true, $args );
		echo '</script>';
	}

	public static function load_component( $path, $file_id, $args = array() ) {
		$script_id = sprintf( 'ccfbw-components-%s', esc_attr( $file_id ) );

		echo '<script type="text/template" id="' . esc_attr( $script_id ) . '">';
		self::get_ccfbw_template( $path, true, $args );
		echo '</script>';
	}

	public static function add_after_section_modals() {
		self::get_settings_template( 'components/modals/delete-item', true );

		if ( ! apply_filters( 'ccfbw_current_editor', false ) ) {
			self::get_settings_template( 'components/modals/selecting-editor', true );
		}
	}

	public static function replace_slashes( $value ) {
		return str_replace( '/', '-', $value );
	}
}
