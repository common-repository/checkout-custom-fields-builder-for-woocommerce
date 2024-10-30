<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

defined( 'ABSPATH' ) || exit;

class Field {

	/**
	 * Field id.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $id = '';

	/**
	 * Field Label.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $label = '';

	/**
	 * Field Type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'text';

	/**
	 * Field Value.
	 *
	 * @since 1.0.0
	 * @var mixed
	 */
	public $value = '';

	/**
	 * Default label field value.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $default_label = '';

	/**
	 * Default value in front-end.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $default_value = '';

	/**
	 * Field Description.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Group key for sorting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $group = 'default';

	/**
	 * Display priority.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	public $priority = 10;

	/**
	 * Icon class for the field.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $icon = '';

	/**
	 * Indication of required field.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $required = false;

	/**
	 * Front-end Validation Type.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $validate = array();

	/**
	 * Autocomplete input form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $autocomplete = array();

	/**
	 * Field markings from wooCommerce.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $woo_field = false;

	/**
	 * Mark field to pro feature.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $is_pro = false;

	/**
	 * Hide/show in form on front-end.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $visible = true;

	/**
	 * Custom class name.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $custom_class_name = '';

	/**
	 * List of fields for editing
	 *
	 * @since 1.0.0
	 * @var array
	 * */
	public $edit_fields = array();

	/**
	 * Field specific parameters.
	 *
	 * @since 1.0.0
	 * @var array
	 * */
	public $properties = array(
		'widget_default_value' => '',
	);

	public function __construct( $data ) {
		if ( $data instanceof Field ) {
			$data = get_object_vars( $data );
		}

		if ( isset( $data['type'] ) ) {
			$this->type = $data['type'];
		}

		$_properties = $this->get_properties();

		foreach ( $_properties as $key => $property ) {
			if ( 'checkboxes' === $property['type'] ) {
				$_options = $property['options'];

				if ( ! empty( $_options ) ) {
					foreach ( $_options as $option_key => $option ) {
						$this->properties[ $key . '_' . $option_key ] = false;
						$_properties[ $key . '_' . $option_key ]      = false;
					}
				}

				continue;
			}

			if ( isset( $this->$key ) ) {
				$this->$key = $property['default'];
			} else {
				$this->properties[ $key ] = $property['default'] ?? '';
			}
		}

		foreach ( $data as $key => $value ) {
			if ( isset( $this->$key ) ) {
				$this->$key = $value;
			} elseif ( isset( $this->properties[ $key ] ) ) {
				$this->properties[ $key ] = $value;
			}
		}

		$this->is_pro      = ! $this->is_free() && ! Load::pro_exist();
		$this->edit_fields = array_keys( $_properties );
	}

	/**
	 * Checking if a field is public.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_free() {
		return in_array( $this->get_type(), Fields::get_type(), true ) || $this->woo_field;
	}

	/**
	 * Get field label.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return empty( $this->label ) ? $this->default_label : $this->label;
	}

	/**
	 * Display required field mark.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function mark_required() {
		return ( ! empty( $this->required ) ) ? 'inline' : 'none';
	}

	/**
	 * Field type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type() {
		return ( ! empty( $this->type ) ) ? $this->type : 'text';
	}

	/**
	 * Field type label.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label_type() {
		if ( $this->woo_field ) {
			$label = __( '[WooCommerce Default field]', 'checkout-custom-fields-builder-for-woocommerce' );
		} else {
			$label = '[' . Fields::get_label( $this->get_type() ) . ']';
		}

		return $label;
	}

	/**
	 * Get a list of fields to edit.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_properties() {
		$field_set = apply_filters( 'ccfbw_field_set', array(), $this->get_type() );
		$fields    = array();

		foreach ( $field_set as $_fields ) {
			$fields = array_merge( $_fields, $fields );
		}

		return $fields;
	}

	/**
	 * Determines whether the field exists in the database.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function exists(): bool {
		return ! empty( $this->id );
	}
}
