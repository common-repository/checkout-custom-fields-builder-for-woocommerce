<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

defined( 'ABSPATH' ) || exit;

class Options {
	public static $option_name = 'ccfbw_settings';

	public static $gutenberg_option_name = 'ccfbw_gutenberg_settings';

	public static $option_editor = 'ccfbw_selected_editor';

	public static $cache_group = 'ccfbw_options';

	public static $edited_date = 'ccfbw_settings_date_edited';

	public function __construct() {
		add_action( 'wp_ajax_ccfbw_save_settings', array( $this, 'save_settings' ) );

		add_action( 'wp_ajax_ccfbw_selecting_editor', array( $this, 'select_editor' ) );

		add_filter( 'ccfbw_get_settings', array( $this, 'get_settings' ) );

		add_filter( 'ccfbw_get_sections', array( $this, 'get_sections' ), 10, 2 );

		add_filter( 'ccfbw_get_section', array( $this, 'get_section' ), 10, 2 );

		add_filter( 'ccfbw_get_fields', array( $this, 'get_fields' ), 10, 2 );

		add_filter( 'ccfbw_current_editor', array( $this, 'get_current_editor' ) );
	}

	public static function get_current_editor() {
		return get_option( self::$option_editor, false );
	}

	public function select_editor() {
		check_ajax_referer( 'ccfbw_selecting_editor' );

		$editor  = ( ! empty( $_POST['editor'] ) ) ? sanitize_text_field( $_POST['editor'] ) : array();
		$message = esc_html__( 'An unexpected error occurred', 'checkout-custom-fields-builder-for-woocommerce' );
		$status  = 'error';

		if ( ! empty( $editor ) ) {
			update_option( self::$option_editor, $editor, 'no' );

			$message = esc_html__( 'Changes saved', 'checkout-custom-fields-builder-for-woocommerce' );
			$status  = 'success';
		}

		$response = array(
			'message' => $message,
			'status'  => $status,
		);

		wp_send_json( $response );
	}

	public static function array_diff_settings( $data1, $data2 ) {
		$result = array();

		$keys1 = wp_list_pluck( $data1, 'id' );
		$keys2 = wp_list_pluck( $data2, 'id' );
		$diff  = array_diff( $keys1, $keys2 );

		if ( ! empty( $diff ) ) {
			foreach ( $diff as $key => $value ) {
				$result[] = $data1[ $key ];
			}
		}

		return $result;
	}

	public static function get_settings() {
		$settings = self::get_saved_settings();

		foreach ( $settings as &$section ) {
			if ( ! empty( $section['fields'] ) ) {
				$fields = $section['fields'];

				foreach ( $fields as &$field ) {
					$field = new Field( $field );
				}

				$section['fields'] = $fields;
			}
		}

		return $settings;
	}

	public static function get_saved_settings() {
		$default_settings = Sections::get_woo_sections();
		$editor           = apply_filters( 'ccfbw_current_editor', false );

		if ( 'elementor' === $editor ) {
			$settings = wp_cache_get( self::$option_name, self::$cache_group );
		} else {
			$settings = wp_cache_get( self::$gutenberg_option_name, self::$cache_group );
		}

		if ( empty( $settings ) ) {
			if ( 'elementor' === $editor ) {
				$settings = get_option( self::$option_name );
			} else {
				$settings = get_option( self::$gutenberg_option_name );
			}

			if ( empty( $settings ) ) {
				$settings = $default_settings;
			} else {
				$diff = self::array_diff_settings( $default_settings, $settings );

				if ( ! empty( $diff ) ) {
					$settings = array_merge( $settings, $diff );
				}
			}

			if ( 'elementor' === $editor ) {
				wp_cache_set( self::$option_name, $settings, self::$cache_group );
			} else {
				wp_cache_set( self::$gutenberg_option_name, $settings, self::$cache_group );
			}
		}

		return $settings;
	}

	public static function get_edited_date( $formatting = false ) {
		$value = get_option( self::$edited_date );

		if ( $formatting && ! empty( $value ) ) {
			/* translators: %s last edited date */
			return sprintf( esc_html__( 'Last Edited: %s', 'checkout-custom-fields-builder-for-woocommerce' ), '<span>' . esc_html( $value ) . '</span>' );
		}

		return $value;
	}

	public static function get_sections( $output, $widget = false ) {
		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! is_admin() || ( in_array( $action, array( 'elementor_ajax', 'elementor' ), true ) ) ) { //phpcs:ignore
			$sections        = self::get_settings();
			$output_sections = array();

			foreach ( $sections as $section ) {
				$fields        = $section['fields'];
				$output_fields = array();

				if ( ! empty( $fields ) ) {
					foreach ( $fields as $field ) {
						if ( ! empty( $field->visible ) ) {
							$output_fields[ $field->id ] = $field;
						}
					}
				}

				if ( $widget ) {
					$section['fields'] = self::add_control_settings( $output_fields, $section['id'] );
				} else {
					$section['fields'] = $output_fields;
				}

				$output_sections[ $section['id'] ] = $section;
			}

			$output = $output_sections;
		}

		return $output;
	}

	public function get_section( $output, $section_id, $widget = false ) {
		$sections = self::get_sections( $widget );

		return $sections[ $section_id ] ?? $output;
	}

	public function get_fields( $output, $section_id, $widget = false ) {
		$section = self::get_section( array(), $section_id, $widget );

		if ( ! empty( $section['fields'] ) ) {
			$fields = wp_list_filter( $section['fields'], array( 'visible' => 1 ) );

			if ( $widget ) {
				$section['fields'] = self::add_control_settings( $fields, $section_id );
			} else {
				$fields = array_map(
					function ( $field_args ) {
						$field        = new Field( $field_args );
						$default_args = get_object_vars( $field );

						return wp_parse_args( $field_args, $default_args );
					},
					$fields
				);
			}

			$output = $fields;
		}

		return $output;
	}

	public static function add_control_settings( $fields, $section_id ) {
		if ( ! empty( $fields ) ) {
			$control_settings = array(
				'billing'  => array(
					'billing_first_name' => 'one_half',
					'billing_last_name'  => 'one_half',
					'billing_company'    => 'full',
					'billing_country'    => 'full',
					'billing_address_1'  => 'full',
					'billing_address_2'  => 'one_half',
					'billing_city'       => 'one_half',
					'billing_state'      => 'one_half',
					'billing_postcode'   => 'one_half',
					'billing_phone'      => 'one_half',
					'billing_email'      => 'one_half',
				),
				'shipping' => array(
					'shipping_first_name' => 'one_half',
					'shipping_last_name'  => 'one_half',
					'shipping_company'    => 'full',
					'shipping_country'    => 'full',
					'shipping_address_1'  => 'full',
					'shipping_address_2'  => 'one_half',
					'shipping_city'       => 'one_half',
					'shipping_state'      => 'one_half',
					'shipping_postcode'   => 'one_half',
				),
				'account'  => array(
					'account_username' => 'one_half',
					'account_password' => 'one_half',
				),
				'order'    => array(
					'order_comments' => 'full',
				),
			);

			foreach ( $fields as $field_id => &$field ) {
				$field->properties['widget_default_value'] = $control_settings[ $section_id ][ $field_id ] ?? 'full';
				$field->visible                            = true;
				$field->default_label                      = $field->label; /* Label default field from woocommerce */
				$field->woo_field                          = true; /* Indication that the field is drawn from woocommerce the plugin */
			}
		}

		return $fields;
	}

	public function save_settings() {
		check_ajax_referer( 'ccfbw_save_settings' );

		$settings = ( ! empty( $_POST['settings'] ) ) ? $this->sanitize_json_data( sanitize_textarea_field( $_POST['settings'] ) ) : array();
		$message  = esc_html__( 'An unexpected error occurred', 'checkout-custom-fields-builder-for-woocommerce' );
		$status   = 'error';

		if ( ! empty( $settings ) ) {
			if ( 'elementor' === self::get_current_editor() ) {
				update_option( self::$option_name, $settings, 'no' );
			} else {
				update_option( self::$gutenberg_option_name, $settings, 'no' );
			}
			update_option( self::$edited_date, wp_date( 'F j, Y H:i' ), 'no' );

			$message = esc_html__( 'Changes saved', 'checkout-custom-fields-builder-for-woocommerce' );
			$status  = 'success';
		}

		$response = array(
			'settings' => apply_filters( 'ccfbw_get_settings', array() ),
			'date'     => self::get_edited_date( true ),
			'message'  => $message,
			'status'   => $status,
		);

		wp_send_json( $response );
	}

	public function sanitize_json_data( $data ): array {
		$sanitized_data = array();

		if ( $data ) {
			$data = json_decode( wp_unslash( $data ), true );

			if ( is_array( $data ) ) {
				$sanitized_data = array_map(
					function ( $section_data ) {
						return $this->sanitize_array( $section_data );
					},
					$data
				);

				$sanitized_data = array_filter( $sanitized_data );
			}
		}

		return $sanitized_data;
	}

	public function sanitize_array( $data ) {
		if ( ! empty( $data ) ) {
			foreach ( $data as $_key => $_value ) {
				if ( is_array( $_value ) ) {
					$data[ $_key ] = $this->sanitize_array( $_value );
				} else {
					$data[ sanitize_key( $_key ) ] = sanitize_text_field( $_value );
				}
			}
		}

		return $data;
	}
}
