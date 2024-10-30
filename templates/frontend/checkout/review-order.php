<?php
/**
 * Review order wrapper
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="ccfbw-checkout-review-order-wrapper">
	<h3 id="ccfbw_order_review_heading"><?php esc_html_e( 'Your order', 'checkout-custom-fields-builder-for-woocommerce' ); ?></h3>
	<?php
		wc_get_template(
			'checkout/review-order.php',
			array(
				'checkout' => WC()->checkout(),
			)
		);
		?>
</div>
<?php
do_action( 'ccfbw_checkout_coupon_form' );
