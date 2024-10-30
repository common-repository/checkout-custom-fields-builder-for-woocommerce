<?php
/**
 * Checkout Payment Section
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Frontend\Elementor;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div class="ccfbw-checkout-review-payment-wrapper">
	<h3 id="ccfbw_order_payment_heading"><?php echo esc_html( Elementor::get_widget_setting( 'payment_title' ) ); ?></h3>
	<div class="ccfbw-section-subtitle">
		<?php echo esc_html( Elementor::get_widget_setting( 'payment_description' ) ); ?>
	</div>

	<?php do_action( 'ccfbw_review_order_payment' ); ?>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
