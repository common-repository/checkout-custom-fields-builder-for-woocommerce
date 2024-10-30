<?php
/**
 * Checkout coupon form
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

$settings = $args['settings'];

?>
<div class="ccfbw-form-coupon-wrapper">
	<div class="woocommerce-form-coupon-toggle">
		<h3><?php echo esc_html( $settings['coupon_section_title'] ); ?></h3>
		<div class="ccfbw-section-subtitle"><?php echo esc_html( $settings['coupon_section_description'] ); ?></div>
		<a href="#" class="showcoupon"><?php echo esc_html( $settings['coupon_open_button'] ); ?></a>
	</div>

	<div class="checkout_coupon woocommerce-form-coupon" style="display:none">

		<p class="form-row form-row-first">
			<label for="coupon_code" id="ccfbw-form-coupon-label" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'checkout-custom-fields-builder-for-woocommerce' ); ?></label>
			<input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'checkout-custom-fields-builder-for-woocommerce' ); ?>" id="coupon_code" value="" />
		</p>

		<p class="form-row form-row-last">
			<button type="button" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php echo esc_attr( $settings['coupon_button_text'] ); ?>">
				<?php echo esc_html( $settings['coupon_button_text'] ); ?>
			</button>
		</p>

		<p class="form-row form-row-wide form-row-coupon-message">
			<span id="ccfbw-form-coupon-message"></span>
		</p>

		<div class="clear"></div>
	</div>
</div>
