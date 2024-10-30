<?php
/**
 * Checkout shipping information form
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var WC_Checkout $checkout */
$checkout = $args['checkout'];

$_section_id    = 'shipping';
$_section       = apply_filters( 'ccfbw_get_section', array(), $_section_id );
$_section_title = ( ! empty( $_section['title'] ) ) ? $_section['title'] : '';
$_section_desc  = ( ! empty( $settings[ 'section_description_' . $_section_id ] ) ) ? $settings[ 'section_description_' . $_section_id ] : '';
?>
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<div class="shipping_address">
			<h3><?php echo esc_html( $_section_title ); ?></h3>
			<div class="ccfbw-section-subtitle">
				<?php echo esc_html( $_section_desc ); ?>
			</div>

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
				$fields = $_section['fields'];

				foreach ( $fields as $key => $field ) {
					woocommerce_form_field( $key, apply_filters( 'ccfbw_shipping_form_field', $field ), $checkout->get_value( $key ) );
				}
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

		</div>

	<?php endif; ?>
</div>
