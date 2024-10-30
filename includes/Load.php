<?php
namespace CCFBW\Woocommerce\Checkout\Builder;

defined( 'ABSPATH' ) || exit;

class Load {

	protected $required_plugin = array(
		'slug' => 'woocommerce',
		'name' => 'WooCommerce',
	);

	protected $elementor_data = array(
		'slug' => 'elementor',
		'name' => 'Elementor',
	);

	protected $plugin_name = '';

	/**
	 * Load Plugin Functionality.
	 *
	 * @param string $plugin_name Current Plugin name.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;

		if ( $this->check_woocommerce() ) {
			new Admin\Load();
			new Frontend\Woocommerce();
			new Frontend\Gutenberg();

			if ( $this->check_elementor() ) {
				new Frontend\Elementor();
			}
		}
	}

	public function check_woocommerce(): bool {
		$response = wp_cache_get( 'check_woocommerce', 'ccfbw_check' );

		if ( false === $response ) {
			$notice = new Admin\Notices(
				$this->plugin_name,
				$this->required_plugin,
				true
			);

			$response = $notice->plugin->is_plugin_activated();

			wp_cache_set( 'check_woocommerce', absint( $response ), 'ccfbw_check' );
		}

		return $response;
	}

	public function check_elementor( $install_link = false ) {
		$response = wp_cache_get( 'check_elementor', 'ccfbw_check' );

		if ( empty( $response ) ) {
			$response = new Admin\Notices(
				$this->plugin_name,
				$this->elementor_data
			);

			wp_cache_set( 'check_elementor', $response, 'ccfbw_check' );
		}

		if ( $install_link ) {
			if ( $response->plugin->is_plugin_installed() ) {
				$_link = $response->plugin->get_plugin_activate_link();
			} else {
				$_link = $response->plugin->get_plugin_install_link();
			}

			return $_link ?? '';
		} else {
			return $response->plugin->is_plugin_activated() ?? false;
		}
	}

	public static function is_true( $name ) {
		return defined( $name ) && self::get_constant( $name );
	}

	public static function get_constant( $name ) {
		if ( defined( $name ) ) {
			return constant( $name );
		}

		return apply_filters( 'ccfbw_constant_default_value', null, $name );
	}

	public static function limit_length( $string, $limit = 127 ) {
		$str_limit = $limit - 3;
		if ( function_exists( 'mb_strimwidth' ) ) {
			if ( mb_strlen( $string ) > $limit ) {
				$string = mb_strimwidth( $string, 0, $str_limit, '...' );
			}
		} else {
			if ( strlen( $string ) > $limit ) {
				$string = substr( $string, 0, $str_limit ) . '...';
			}
		}
		return $string;
	}
}
