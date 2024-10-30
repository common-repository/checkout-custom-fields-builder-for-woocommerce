<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Frontend;

use Elementor\Plugin;
use Elementor\Widget_Base;
use WC_AJAX;
use WC_Cart;
use WC_Customer;
use CCFBW\Woocommerce\Checkout\Builder\Load;
use Exception;

class Elementor {

	public static $post_widget_setting = 'ccfbw_widget_form';

	public function __construct() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ), 20 );

		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render_content' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'before_enqueue_scripts' ), 99 );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'elementor_editor_init_cart' ) );

		add_action( 'elementor/elements/categories_registered', array( $this, 'categories_registered' ) );

		$action  = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$preview = isset( $_REQUEST['elementor-preview'] ) ? sanitize_text_field( $_REQUEST['elementor-preview'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'elementor' === $action && is_admin() ) {
			add_action( 'init', array( Woocommerce::class, 'register_hooks' ), 5 );
		}

		// Show checkout form when editing if cart is empty.
		if (
			( 'elementor' === $action && is_admin() ) // Elementor Editor
			|| 'elementor_ajax' === $action // Elementor Editor Preview - Ajax Render Widget
			|| ( ! empty( $preview ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			add_filter( 'woocommerce_checkout_redirect_empty_cart', '__return_false', 5 );
		}
	}

	/**
	 * Register widget in Elementor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	public function register_widgets( $widgets_manager ) {
		if ( Load::is_true( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			$widgets_manager->register( new Widgets\CCFBW_Elementor_Widget() );
		} else {
			$widgets_manager->register_widget_type( new Widgets\CCFBW_Elementor_Widget() );
		}
	}

	/**
	 * Start saving before widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	public function before_render_content( Widget_Base $widget ) {
		if ( 'ccfbw_form' === $widget->get_name() ) {
			$this->set_settings( $widget->get_settings_for_display() );
		}
	}

	/**
	 * Saving settings to meta to use settings outside of widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	private function set_settings( $settings ) {
		update_post_meta( Woocommerce::get_checkout_page_id(), self::$post_widget_setting, $settings );
	}

	/**
	 * Get widget setting by key.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public static function get_widget_setting( $setting, $default = null ) {
		$data = self::get_widget_settings();

		return $data[ $setting ] ?? $default;
	}

	/**
	 * Get a list of widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public static function get_widget_settings() {
		return get_post_meta( Woocommerce::get_checkout_page_id(), self::$post_widget_setting, true );
	}

	/**
	 * Registering scripts and styles for output on the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 * */
	public function before_enqueue_scripts() {
		wp_register_style( 'ccfbw-form-style', CCFBW_ASSETS_URL . 'dist/css/frontend.css', array(), CCFBW_VERSION );

		$suffix = Load::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';

		wp_enqueue_script( 'wc-country-select' );

		wp_register_script( 'ccfbw-checkout-form', CCFBW_ASSETS_URL . 'dist/js/checkout.js', array(), CCFBW_VERSION, true );

		wp_localize_script(
			'ccfbw-checkout-form',
			'ccfbw_address_i18n_params',
			array(
				'locale'             => wp_json_encode( WC()->countries->get_country_locale() ),
				'locale_fields'      => wp_json_encode( WC()->countries->get_country_locale_field_selectors() ),
				'i18n_required_text' => esc_attr__( 'required', 'checkout-custom-fields-builder-for-woocommerce' ),
				'i18n_optional_text' => esc_html__( 'optional', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		wp_register_script( 'ccfbw-checkout', apply_filters( 'woocommerce_get_asset_url', plugins_url( 'assets/js/frontend/checkout' . $suffix . '.js', WC_PLUGIN_FILE ), 'assets/js/frontend/checkout' . $suffix . '.js' ), array(), CCFBW_VERSION, true );

		wp_localize_script(
			'ccfbw-checkout-form',
			'wc_checkout_params',
			array(
				'ajax_url'                  => WC()->ajax_url(),
				'wc_ajax_url'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
				'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
				'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
				'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
				'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
				'checkout_url'              => WC_AJAX::get_endpoint( 'checkout' ),
				'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
				'debug_mode'                => Load::is_true( 'WP_DEBUG' ),
				/* translators: %s: Order history URL on My Account section */
				'i18n_checkout_error'       => sprintf( esc_attr__( 'There was an error processing your order. Please check for any charges in your payment method and review your <a href="%s">order history</a> before placing the order again.', 'checkout-custom-fields-builder-for-woocommerce' ), esc_url( wc_get_account_endpoint_url( 'orders' ) ) ),
			)
		);

		wp_deregister_script( 'wc-address-i18n' );
	}

	/**
	 * Initializing the WooCommerce Cart When Editing the Checkout Form in the Widget.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function elementor_editor_init_cart() {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		if ( ! $has_cart ) {
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
			WC()->session  = new $session_class();
			WC()->session->init();
			WC()->cart     = new WC_Cart();
			WC()->customer = new WC_Customer( get_current_user_id(), true );
		}

		wp_enqueue_style( 'ccfbw-icons-style', CCFBW_ASSETS_URL . 'dist/css/icons.css', array( 'elementor-icons' ), CCFBW_VERSION );
	}

	/**
	 * Registering a category for a widget.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function categories_registered() {
		Plugin::instance()->elements_manager->add_category(
			'ccfbw-elements',
			array(
				'title' => esc_html__( 'WuCheckout', 'checkout-custom-fields-builder-for-woocommerce' ),
				'icon'  => '',
			)
		);
	}
}
