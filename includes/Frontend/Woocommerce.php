<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Frontend;

use CCFBW\Woocommerce\Checkout\Builder\Admin\Field;
use WC_Checkout;
use WC_Coupon;
use CCFBW\Woocommerce\Checkout\Builder\Admin\Sections;
use CCFBW\Woocommerce\Checkout\Builder\Templates;

class Woocommerce {
	public function __construct() {
		add_action( 'woocommerce_checkout_init', array( $this, 'checkout_init' ), 20 );

		add_action( 'wc_ajax_ccfbw_apply_coupon', array( $this, 'apply_coupon' ) );

		add_action( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ), 20, 2 );

		add_action( 'ccfbw_review_order_payment', array( $this, 'review_order_payment' ) );

		add_filter( 'wc_get_template', array( $this, 'replace_checkout_form' ), 99, 2 );

		/* START Load hooks after Woocommerce ajax request. */
		add_action( 'woocommerce_checkout_update_order_review', array( self::class, 'load_widget_before_wc_ajax' ) );
		add_action( 'woocommerce_before_calculate_totals', array( self::class, 'load_widget_before_wc_ajax' ) );
		/* END */

		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ), 30, 2 );
	}

	/**
	 * Overwrite field settings from woocommerce.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	public function checkout_init( WC_Checkout $checkout ) {
		global $ccfbw_load;

		if ( ! is_admin() || ( $ccfbw_load->check_elementor() && \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
			$checkout_fields = $checkout->__get( 'checkout_fields' );

			foreach ( $checkout->__get( 'checkout_fields' ) as $section_key => $field ) {
				$fields = apply_filters( 'ccfbw_get_fields', array(), $section_key );
				if ( ! empty( $fields ) ) {
					$checkout_fields[ $section_key ] = $fields;
				}
			}

			$checkout->__set( 'checkout_fields', $checkout_fields );
		}

		new Sections();
	}

	/**
	 * AJAX apply coupon on checkout page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function apply_coupon() {
		check_ajax_referer( 'apply-coupon', 'security' );

		$coupon_code   = sanitize_text_field( $_POST['coupon_code'] );
		$billing_email = sanitize_email( $_POST['billing_email'] );
		$response      = array(
			'status'  => 'error',
			'message' => sprintf(
			/* translators: %s coupon code */
				esc_html__( 'Coupon "%s" does not exist!', 'checkout-custom-fields-builder-for-woocommerce' ),
				$coupon_code
			),
		);

		if ( is_string( $billing_email ) && is_email( $billing_email ) ) {
			wc()->customer->set_billing_email( $billing_email );
		}

		if ( ! ( '' === $coupon_code || is_null( $coupon_code ) || ctype_space( $coupon_code ) ) ) {
			$apply = WC()->cart->add_discount( wc_format_coupon_code( wp_unslash( $coupon_code ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Get the coupon.
			$the_coupon = new WC_Coupon( $coupon_code );

			if ( $apply ) {
				$response['status']  = 'success';
				$response['message'] = $the_coupon->get_coupon_message( WC_Coupon::WC_COUPON_SUCCESS );
			} else {
				$error_messages = wc_get_notices( 'error' );

				if ( ! empty( $error_messages ) ) {
					$response['message'] = reset( $error_messages )['notice'];
				}
			}
		} else {
			$response['message'] = WC_Coupon::get_generic_coupon_error( WC_Coupon::E_WC_COUPON_PLEASE_ENTER );
		}

		wp_send_json( $response );

		wc_print_notices();
		wp_die();
	}

	/**
	 * Replace coupon title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function coupon_label( $label, WC_Coupon $coupon ) {
		return str_replace( $coupon->get_code(), '<span>' . $coupon->get_code() . '</span>', $label );
	}

	/**
	 * Getting a template with a list of payments & button place order.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function review_order_payment() {
		Templates::get_ccfbw_template( 'checkout/payment-actions', true );
	}

	/**
	 * Replace checkout templates.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function replace_checkout_form( $template, $template_name ) {
		global $wp_query;

		if ( 'checkout/form-checkout.php' === $template_name ) {
			$template = apply_filters( 'ccfbw_template_file', CCFBW_PATH, $template_name ) . '/templates/frontend/' . $template_name;
		} elseif ( 'checkout/payment.php' === $template_name ) {
			$wc_ajax = $wp_query->get( 'wc-ajax' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( in_array( $wc_ajax, array( 'update_order_review', 'update_shipping_method' ), true ) ) {
				$template = apply_filters( 'ccfbw_template_file', CCFBW_PATH, $template_name ) . '/templates/frontend/checkout/payment-actions.php';
			} else {
				$template = apply_filters( 'ccfbw_template_file', CCFBW_PATH, $template_name ) . '/templates/frontend/' . $template_name;
			}
		}

		return $template;
	}

	/**
	 * Load filters & actions used in the widget to process them after the Ajax request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function load_widget_before_wc_ajax() {
		global $ccfbw_load, $wp_query;

		if ( ! $ccfbw_load->check_elementor() ) {
			return;
		}

		$wc_ajax = $wp_query->get( 'wc-ajax' ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! $wc_ajax ) {
			return;
		}

		if ( ! in_array( $wc_ajax, array( 'update_order_review', 'update_shipping_method' ), true ) ) {
			return;
		}

		// Check ajax nonce
		if ( 'update_order_review' === $wc_ajax ) {
			check_ajax_referer( 'update-order-review', 'security' );
		} else {
			check_ajax_referer( 'update-shipping-method', 'security' );
		}

		$page_id = self::get_checkout_page_id();

		// Return empty if no $page_id.
		if ( ! $page_id ) {
			return;
		}

		$document = \Elementor\Plugin::instance()->documents->get_doc_for_frontend( $page_id );

		// Return empty if not Elementor page.
		if ( ! $document ) {
			return;
		}

		// Setup $page_id as the WP global $post, so is available to widget.
		$post = get_post( $page_id );
		setup_postdata( $post );

		$document_data = $document->get_elements_data();
		\Elementor\Plugin::instance()->db->iterate_data(
			$document_data,
			function( $element ) use ( &$widget_data ) {
				if ( $widget_data && ( ! isset( $element['widgetType'] ) || ! in_array( $element['widgetType'], array( 'ccfbw_form' ), true ) ) ) {
					return;
				}
				$widget_data = $element;
			}
		);

		if ( $widget_data ) {
			$widget_instance = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $widget_data );
			if ( method_exists( $widget_instance, 'add_render_elements' ) ) {
				$widget_instance->add_render_elements();
			}
		}
	}

	/**
	 * Save custom field values in the order.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_order_meta( $order_id, $data ) {
		$order = wc_get_order( $order_id );

		if ( ! $order->get_id() ) {
			return;
		}

		$_sections = apply_filters( 'ccfbw_get_sections', array() );

		if ( ! empty( $_sections ) ) {
			$save = false;

			foreach ( $_sections as $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$field = new Field( $field );

						/**
						* Exclude Woocommerce fields from saving.
						* They are saved in Woocommerce itself.
						*/
						if ( $this->get_current_prefix_field( $field->id, $section['id'] . '_' ) ) {
							continue;
						}

						if ( ! empty( $data[ $field->id ] ) ) {
							$order->update_meta_data( '_' . $field->id, $data[ $field->id ] );

							$save = true;
						}
					}
				}
			}

			if ( $save ) {
				$order->save();
			}
		}
	}

	/**
	 * Get the field prefix assigned at creation or taken from woo.
	 *
	 * @since 1.0.0
	 *
	 * @return bool|string
	 */
	public function get_current_prefix_field( $string, $prefix ) {
		if ( function_exists( 'str_starts_with' ) ) {
			$found = str_starts_with( $string, $prefix );
		} else {
			$found = substr( $string, 0, strlen( $prefix ) );
		}

		return $found;
	}

	/**
	 * Load Woocommerce files to work out the order form when editing the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register_hooks() {
		WC()->frontend_includes();
	}

	/**
	 * Get a template with order information.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function order_review() {
		Templates::get_ccfbw_template( 'checkout/review-order', true );
	}

	/**
	 * Get checkout page ID.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public static function get_checkout_page_id() {
		return wc_get_page_id( 'checkout' );
	}
}
