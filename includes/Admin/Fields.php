<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

defined( 'ABSPATH' ) || exit;

use CCFBW\Woocommerce\Checkout\Builder\Templates;
use WC_Checkout;

class Fields {
	public static $new_fields = array();

	public static function init() {
		add_filter( 'ccfbw_field_set', array( self::class, 'get_field_set' ), 10, 2 );

		$fields = self::get_fields_data();

		foreach ( $fields as $field ) {
			if ( empty( $field ) ) {
				continue;
			}

			$field['default_label'] = apply_filters( 'ccfbw_field_label', self::get_label( $field['type'] ) );

			self::add( $field );
		}

		add_action( 'ccfbw_settings_content_after', array( self::class, 'get_field_modal' ) );
	}

	/**
	 * Add properties and methods to a field
	 *
	 * @return void
	 * */
	public static function add( $_args ) {
		$_field = new Field( $_args );

		if ( $_field->is_pro ) {
			return;
		}

		self::$new_fields[] = $_field;
	}

	/**
	 * Get all types field
	 *
	 * @return array
	 * */
	public static function get_fields_data() {
		$all_fields = array(
			array(
				'type'  => 'text',
				'label' => __( 'Text Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-text',
			),
			array(
				'type'  => 'password',
				'label' => __( 'Password Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-text',
			),
			array(
				'type'  => 'country',
				'label' => __( 'Text Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-country',
			),
			array(
				'type'  => 'state',
				'label' => __( 'Text Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-state',
			),
			array(
				'type'  => 'textarea',
				'label' => __( 'Textarea', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-textarea',
			),
			array(
				'type'  => 'number',
				'label' => __( 'Number', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => 'ccfbw-icon-field-number',
			),
			array(
				'type'         => 'tel',
				'label'        => __( 'Phone number', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate'     => array( 'phone' ),
				'autocomplete' => 'tel',
				'icon'         => 'ccfbw-icon-field-phone',
			),
			array(
				'type'         => 'email',
				'label'        => __( 'Email', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate'     => array( 'email' ),
				'autocomplete' => 'email',
				'icon'         => 'ccfbw-icon-field-email',
			),
			array(
				'type'         => 'url',
				'label'        => __( 'URL', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate'     => array( 'url' ),
				'autocomplete' => 'url',
				'icon'         => 'ccfbw-icon-field-url',
			),
			array(
				'type'     => 'file',
				'label'    => __( 'File Upload', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'file' ),
				'icon'     => 'ccfbw-icon-field-file-upload',
			),
			array(
				'type'     => 'select',
				'label'    => __( 'Drop-Down', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'select' ),
				'icon'     => 'ccfbw-icon-field-select',
			),
			array(
				'type'     => 'radio',
				'label'    => __( 'Radio Button', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'radio' ),
				'icon'     => 'ccfbw-icon-field-radio',
			),
			array(
				'type'     => 'multi-checkbox',
				'label'    => __( 'Multiple Checkbox', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'multi-checkbox' ),
				'icon'     => 'ccfbw-icon-field-multi-checkbox',
			),
			array(
				'type'     => 'multi-select',
				'label'    => __( 'Multiple Select', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'multi-select' ),
				'icon'     => 'ccfbw-icon-field-multi-select',
			),
			array(
				'type'     => 'date',
				'label'    => __( 'Date picker', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'date' ),
				'icon'     => 'ccfbw-icon-field-date-picker',
			),
			array(
				'type'     => 'time',
				'label'    => __( 'Time picker', 'checkout-custom-fields-builder-for-woocommerce' ),
				'validate' => array( 'time' ),
				'icon'     => 'ccfbw-icon-field-time-picker',
			),
		);

		return apply_filters( 'ccfbw_data_fields', $all_fields );
	}

	/**
	 * List of field labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_labels() {
		return wp_list_pluck( self::get_fields_data(), 'label', 'type' );
	}

	/**
	 * Name fields group for edit panel
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 * */
	public static function get_group_name( $group ) {
		$data = array(
			'advanced' => __( 'Advanced', 'checkout-custom-fields-builder-for-woocommerce' ),
		);

		return $data[ $group ] ?? '';
	}

	/**
	 * Get field set.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_field_set( $response, $type ) {
		$data = array(
			'text'     => array(
				'general'  => array(
					'default_value' => array(
						'type'        => 'text',
						'label'       => __( 'Default Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter default value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'max_length'    => array(
						'type'        => 'number',
						'label'       => __( 'Max Length', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter max length', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
						'step'        => 10,
						'min'         => 0,
						'max'         => 100,
					),
				),
				'advanced' => array(
					'allow_characters' => array(
						'type'        => 'text',
						'label'       => __( 'Allowed Characters (Regex)', 'checkout-custom-fields-builder-for-woocommerce' ),
						'description' => __( 'Use regular expression', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'error_message'    => array(
						'type'        => 'text',
						'label'       => __( 'Error Message', 'checkout-custom-fields-builder-for-woocommerce' ),
						'description' => __( 'Only for Allowed Characters Validation', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
				),
			),
			'textarea' => array(
				'general'  => array(
					'default_value' => array(
						'type'        => 'text',
						'label'       => __( 'Default Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter default value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'max_length'    => array(
						'type'        => 'number',
						'label'       => __( 'Max Length', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter max length', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'rows'          => array(
						'type'        => 'number',
						'label'       => __( 'Rows', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter rows', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => 2,
						'required'    => false,
					),
				),
				'advanced' => array(
					'allow_characters' => array(
						'type'        => 'text',
						'label'       => __( 'Allowed Characters (Regex)', 'checkout-custom-fields-builder-for-woocommerce' ),
						'description' => __( 'Use regular expression', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'error_message'    => array(
						'type'        => 'text',
						'label'       => __( 'Error Message', 'checkout-custom-fields-builder-for-woocommerce' ),
						'description' => __( 'Only for Allowed Characters Validation', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
				),
			),
			'number'   => array(
				'general'  => array(
					'default_value' => array(
						'type'        => 'text',
						'label'       => __( 'Default Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter default value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'min_value'     => array(
						'type'        => 'number',
						'label'       => __( 'Min. Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter min. value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'max_value'     => array(
						'type'        => 'number',
						'label'       => __( 'Max. Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter max. value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => false,
					),
					'step_value'    => array(
						'type'        => 'number',
						'label'       => __( 'Step Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter step value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => 1,
						'required'    => false,
					),
				),
				'advanced' => array(),
			),
			'tel'      => array(
				'general'  => array(),
				'advanced' => array(
					'validation_text' => array(
						'type'     => 'text',
						'label'    => __( 'Validation Error Message', 'checkout-custom-fields-builder-for-woocommerce' ),
						'required' => false,
					),
				),
			),
			'email'    => array(
				'general'  => array(),
				'advanced' => array(
					'validation_text' => array(
						'type'     => 'text',
						'label'    => __( 'Validation Error Message', 'checkout-custom-fields-builder-for-woocommerce' ),
						'required' => false,
					),
				),
			),
			'url'      => array(
				'general'  => array(
					'default_value' => array(
						'type'        => 'text',
						'label'       => __( 'Default Value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'placeholder' => __( 'Enter default value', 'checkout-custom-fields-builder-for-woocommerce' ),
						'default'     => '',
						'required'    => true,
					),
				),
				'advanced' => array(),
			),
			'country'  => array(
				'general'  => array(),
				'advanced' => array(),
			),
			'state'    => array(
				'general'  => array(),
				'advanced' => array(),
			),
			'password' => array(
				'general'  => array(),
				'advanced' => array(),
			),
		);

		if ( array_key_exists( $type, $data ) ) {
			if ( in_array( $type, array( 'text', 'textarea', 'number', 'tel', 'email', 'url', 'country', 'state', 'password' ), true ) ) {
				$default_fields = self::get_default_field_set();

				foreach ( $default_fields as $group => $_fields ) {
					$data[ $type ][ $group ] = wp_parse_args( $data[ $type ][ $group ], $_fields );
				}
			}

			$response = $data[ $type ];
		} elseif ( Load::pro_exist() ) {
			$response = apply_filters( 'ccfbw_pro_field_set', $response, $type );
		}

		return $response;
	}

	/**
	 * Get default field set.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_default_field_set() {
		$fields = array(
			'general'  => array(
				'label'       => array(
					'type'        => 'text',
					'label'       => __( 'Label', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Enter label', 'checkout-custom-fields-builder-for-woocommerce' ),
					'column'      => 'start',
					'default'     => '',
					'required'    => true,
				),
				'placeholder' => array(
					'type'        => 'text',
					'label'       => __( 'Placeholder', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Enter placeholder', 'checkout-custom-fields-builder-for-woocommerce' ),
					'column'      => 'end',
					'required'    => false,
				),
				'description' => array(
					'type'        => 'text',
					'label'       => __( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Enter description', 'checkout-custom-fields-builder-for-woocommerce' ),
					'default'     => '',
					'required'    => false,
				),
				'position'    => array(
					'type'        => 'select',
					'label'       => __( 'Position', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Select...', 'checkout-custom-fields-builder-for-woocommerce' ),
					'properties'  => array(
						'options' => array(
							array(
								'label' => __( 'Contact Information', 'checkout-custom-fields-builder-for-woocommerce' ),
								'value' => 'checkout-contact-information-block',
							),
							array(
								'label' => __( 'Billing Address', 'checkout-custom-fields-builder-for-woocommerce' ),
								'value' => 'checkout-billing-address-block',
							),
							array(
								'label' => __( 'Shipping Address', 'checkout-custom-fields-builder-for-woocommerce' ),
								'value' => 'checkout-shipping-address-block',
							),
							array(
								'label' => __( 'Shipping Methods', 'checkout-custom-fields-builder-for-woocommerce' ),
								'value' => 'checkout-shipping-methods-block',
							),
						),
					),
					'default'     => 'checkout-contact-information-block',
					'required'    => false,
				),
				'visible'     => array(
					'type'     => 'checkbox',
					'label'    => __( 'Enable/Disable Field', 'checkout-custom-fields-builder-for-woocommerce' ),
					'default'  => true,
					'required' => false,
				),
				'required'    => array(
					'type'     => 'checkbox',
					'label'    => __( 'Is Required', 'checkout-custom-fields-builder-for-woocommerce' ),
					'default'  => false,
					'required' => false,
				),
			),
			'advanced' => array(
				'custom_class_name' => array(
					'type'     => 'text',
					'label'    => __( 'Custom Class Name', 'checkout-custom-fields-builder-for-woocommerce' ),
					'default'  => '',
					'required' => false,
				),
			),
		);

		if ( 'elementor' === apply_filters( 'ccfbw_current_editor', false ) ) {
			unset( $fields['general']['position'] );
		}

		return $fields;
	}

	/**
	 * Get choice field set.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_choice_field_set() {
		return array(
			'general'  => array(
				'placeholder'   => array(
					'type'        => 'text',
					'label'       => __( 'Placeholder', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Enter placeholder', 'checkout-custom-fields-builder-for-woocommerce' ),
					'column'      => 'end',
					'default'     => __( 'Choose an option', 'checkout-custom-fields-builder-for-woocommerce' ),
					'required'    => false,
				),
				'options'       => array(
					'type'         => 'options',
					'labels'       => array(
						'name'  => __( 'Label', 'checkout-custom-fields-builder-for-woocommerce' ),
						'value' => __( 'Value *', 'checkout-custom-fields-builder-for-woocommerce' ),
					),
					'placeholders' => array(
						'name'  => __( 'Label', 'checkout-custom-fields-builder-for-woocommerce' ),
						'value' => __( 'Value', 'checkout-custom-fields-builder-for-woocommerce' ),
					),
					'default'      => array(
						array(
							'label' => '',
							'value' => '',
						),
						array(
							'label' => '',
							'value' => '',
						),
						array(
							'label' => '',
							'value' => '',
						),
					),
					'required'     => false,
				),
				'default_value' => array(
					'type'        => 'select',
					'label'       => __( 'Default Value', 'checkout-custom-fields-builder-for-woocommerce' ),
					'placeholder' => __( 'Select...', 'checkout-custom-fields-builder-for-woocommerce' ),
					'default'     => '',
					'required'    => false,
				),
			),
			'advanced' => array(),
		);
	}

	/**
	 * Get field label by type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_label( $type ) {
		$data = self::get_labels();

		return $data[ $type ] ?? '';
	}

	/**
	 * List of field types.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_types() {
		return array(
			'free' => array(
				'text',
				'textarea',
				'number',
				'tel',
				'email',
				'url',
				'country',
				'state',
			),
			'pro'  => array(
				'file',
				'select',
				'radio',
				'multi-checkbox',
				'multi-select',
				'date',
				'time',
			),
		);
	}

	/**
	 * Get field type by group.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_type( $group = 'free' ) {
		$data = self::get_types();

		return $data[ $group ] ?? array();
	}

	public static function get() {
		return apply_filters( 'ccfbw_fields', self::$new_fields );
	}

	public static function get_woo_fields( $section_id ) {
		$fields = array();

		if ( 'elementor' === apply_filters( 'ccfbw_current_editor', false ) ) {
			$checkout = WC_Checkout::instance();
			$fields   = $checkout->get_checkout_fields( $section_id );

			if ( ! empty( $fields ) && is_array( $fields ) ) {
				foreach ( $fields as $field_key => &$field ) {
					$field = new Field( $field );

					$field->woo_field = true;
					$field->id        = $field_key;
				}
			}
		}

		return ( ! empty( $fields ) ) ? array_values( $fields ) : array();
	}

	public static function get_field_modal() {
		Templates::get_settings_template( 'components/field/edit-panel', true );
	}
}
