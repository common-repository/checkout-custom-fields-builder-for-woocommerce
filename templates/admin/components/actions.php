<?php
/**
 * Template for displaying save and edit buttons
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="ccfbw-top-actions__row">
	<div class="ccfbw-top-column ccfbw-top-actions__left">
		<?php
		$checkout = get_post( wc_get_page_id( 'checkout' ) );

		if ( $checkout ) :
			?>
			<a href="<?php echo esc_url( get_edit_post_link( wc_get_page_id( 'checkout' ) ) ); ?>" target="_blank" class="button ccfbw-edit-page-link">
				<?php esc_html_e( 'Edit checkout page', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
			</a>
		<?php else : ?>
			<button class="button ccfbw-edit-page-button" disabled>
				<?php esc_html_e( 'Edit checkout page', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
			</button>
		<?php endif; ?>
	</div>
	<div class="ccfbw-top-column ccfbw-top-actions__center"></div>
	<div class="ccfbw-top-column ccfbw-top-actions__right">
		<div class="ccfbw-save-message"></div>
		<?php submit_button( __( 'Save Changes', 'checkout-custom-fields-builder-for-woocommerce' ), 'primary', 'ccfbw-settings-save', false ); ?>
	</div>
</div>
