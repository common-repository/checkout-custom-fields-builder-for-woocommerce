<?php
/**
 * Modal edit field
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Admin\Load;
?>
<div class="ccfbw-field__modal">
	<div class="ccfbw-field__modal--content">
		<button
				type="button"
				aria-label="<?php esc_attr_e( 'Close', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"
				class="ccfbw-modal__close"
				data-ccfbw-action="close">
			<i class="ccfbw-icon-closing-cross"></i>
		</button>
		<div class="ccfbw-field__modal--top">
			<div class="ccfbw-field__modal--title"></div>
		</div>
		<div class="ccfbw-field__modal--body" id="ccfbw-field-scrollbar"></div>
		<div class="ccfbw-field__modal--bottom">
			<div class="ccfbw-field__modal--column">
				<button type="button" class="button button-primary ccfbw-field__save" data-ccfbw-action="save">
					<?php esc_html_e( 'Save', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
				</button>
				<button type="button" class="button ccfbw-field__close" data-ccfbw-action="close">
					<?php esc_html_e( 'Cancel', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
