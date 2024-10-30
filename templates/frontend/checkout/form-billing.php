<?php
/**
 * Checkout billing information form
 *
 * @var array $args
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var WC_Checkout $checkout */
$checkout = $args['checkout'];
$settings = $args['settings'];

$_section_id    = 'billing';
$_section       = apply_filters( 'ccfbw_get_section', array(), $_section_id );
$_section_title = ( ! empty( $_section['title'] ) ) ? $_section['title'] : '';
$_section_desc  = ( ! empty( $settings[ 'section_description_' . $_section_id ] ) ) ? $settings[ 'section_description_' . $_section_id ] : '';
?>
<div class="woocommerce-billing-fields">
	<h3><?php echo esc_html( $_section_title ); ?></h3>
	<div class="ccfbw-section-subtitle">
		<?php echo esc_html( $_section_desc ); ?>
	</div>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $_section['fields'];

		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, apply_filters( 'ccfbw_billing_form_field', $field ), $checkout->get_value( $key ) );
		}
		?>
	</div>

	<h3 id="ship-to-different-address">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
			<input id="ship-to-different-address-checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
			<span>
				<?php
				if ( isset( $settings['shipping_open_checkbox_text'] ) ) {
					echo esc_html( $settings['shipping_open_checkbox_text'] );
				} else {
					esc_html_e( 'Ship to a different address?', 'checkout-custom-fields-builder-for-woocommerce' );
				}
				?>
			</span>
		</label>
	</h3>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>
