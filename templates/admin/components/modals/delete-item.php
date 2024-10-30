<?php
/**
 * Template for field delete popup
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="ccfbw-modal" id="ccfbw-modal-delete-item">
	<div class="ccfbw-modal__dialog ccfbw-modal__centered">
		<div class="ccfbw-modal__content">
			<button type="button" aria-label="Close" data-ccfbw-action="close"class="ccfbw-modal__close">
				<i class="ccfbw-icon-closing-cross"></i>
			</button>
			<div class="ccfbw-modal__header">
				<h3><?php esc_html_e( 'Delete', 'checkout-custom-fields-builder-for-woocommerce' ); ?></h3>
			</div>
			<div class="ccfbw-modal__body">
				<p>
					<?php esc_html_e( 'Are you sure you want to delete this section and all items in it?', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
				</p>
			</div>
			<div class="ccfbw-modal__footer">
				<div class="ccfbw-modal__actions">
					<button class="button ccfbw-button ccfbw-button__cancel" data-ccfbw-action="close">
						<?php esc_html_e( 'Cancel', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
					</button>
					<button type="button" class="ccfbw-button ccfbw-button__delete" data-ccfbw-action="delete">
						<?php esc_html_e( 'Yes, Delete', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
