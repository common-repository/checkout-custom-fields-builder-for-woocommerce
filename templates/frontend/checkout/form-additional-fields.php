<?php
/**
 * Checkout additional fields
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var WC_Checkout $checkout */
$checkout = $args['checkout'];
$settings = $args['settings'];

$_section_id    = 'order';
$_section       = apply_filters( 'ccfbw_get_section', array(), $_section_id );
$_section_title = ( ! empty( $_section['title'] ) ) ? $_section['title'] : '';
$_section_desc  = ( ! empty( $settings[ 'section_description_' . $_section_id ] ) ) ? $settings[ 'section_description_' . $_section_id ] : '';
?>
<div class="woocommerce-additional-fields">
	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>
	<h3><?php echo esc_html( $_section_title ); ?></h3>
	<div class="ccfbw-section-subtitle">
		<?php echo esc_html( $_section_desc ); ?>
	</div>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

			<h3><?php esc_html_e( 'Additional information', 'checkout-custom-fields-builder-for-woocommerce' ); ?></h3>

		<?php endif; ?>

		<div class="woocommerce-additional-fields__field-wrapper">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?php woocommerce_form_field( $key, apply_filters( 'ccfbw_additional_form_field', $field ), $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
