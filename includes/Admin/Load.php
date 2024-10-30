<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

use CCFBW\Woocommerce\Checkout\Builder\Templates;

defined( 'ABSPATH' ) || exit;

class Load {
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

		add_action( 'admin_init', array( $this, 'init' ) );

		add_action( 'ccfbw_settings_screen_after', array( Templates::class, 'add_after_section_modals' ) );

		add_action( 'admin_init', array( $this, 'load_field_set' ) );

		new Options();
	}

	public function init() {
		if ( false === get_option( 'ccfbw_install_date' ) ) {
			add_option( 'ccfbw_install_date', wp_date( 'd-m-Y' ), '', 'no' );
		}
	}

	public function load_field_set() {
		Fields::init();
	}

	public function load() {
		if ( ! is_textdomain_loaded( CCFBW_PLUGIN_SLUG ) ) {
			load_plugin_textdomain( CCFBW_PLUGIN_SLUG, false, CCFBW_PLUGIN_SLUG . '/languages' );
		}

		Admin_Menu::init();
	}

	public function get_components(): array {
		$templates = array(
			'field' => array(
				array(
					'name' => 'list-actions',
					'args' => array(),
				),
			),
		);

		$field_types = Fields::get_fields_data();

		if ( ! empty( $field_types ) ) {
			foreach ( $field_types as $item ) {
				$templates['field'][] = array(
					'name' => 'edit-fields',
					'args' => array(
						'type' => $item['type'],
					),
				);
			}
		}

		return $templates;
	}

	public static function pro_exist(): bool {
		return defined( 'CCFBW_PRO_VERSION' );
	}

	public function register_scripts() {
		wp_register_style( 'ccfbw-settings', CCFBW_ASSETS_URL . 'dist/css/admin.css', array(), CCFBW_VERSION );

		wp_register_script( 'ccfbw-settings', CCFBW_ASSETS_URL . 'dist/js/admin.js', array( 'jquery' ), CCFBW_VERSION, true );

		wp_localize_script( 'ccfbw-settings', 'ccfbw_settings', apply_filters( 'ccfbw_get_settings', array() ) );

		global $ccfbw_load;

		wp_localize_script(
			'ccfbw-settings',
			'ccfbw_additional_settings',
			array(
				'editor'    => apply_filters( 'ccfbw_current_editor', false ),
				'elementor' => array(
					'activate' => $ccfbw_load->check_elementor( true ),
				),
			)
		);

		wp_localize_script( 'ccfbw-settings', 'ccfbw_new_fields', Fields::get() );

		foreach ( $this->get_components() as $folder => $templates ) {
			foreach ( $templates as $template ) {
				$args = ( ! empty( $template['args'] ) ) ? $template['args'] : array();
				$type = ( ! empty( $args['type'] ) ) ? '-' . $args['type'] : '';

				$path    = 'components/' . $folder . '/' . $template['name'];
				$file_id = Templates::replace_slashes( $folder . '-' . $template['name'] . $type );

				Templates::load_admin_component( $path, $file_id, $args );
			}
		}
	}
}
