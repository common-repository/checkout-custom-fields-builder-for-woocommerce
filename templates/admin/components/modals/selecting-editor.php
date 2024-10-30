<?php
/**
 * Popup selecting editor
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="ccfbw-modal" id="ccfbw-modal-selecting-editor">
	<div class="ccfbw-modal__dialog ccfbw-modal__centered">
		<div class="ccfbw-modal__content">
			<div class="ccfbw-modal__body">
				<form method="POST" class="ccfbw-modal__selecting">
					<?php wp_nonce_field( 'ccfbw_selecting_editor', 'nonce-selecting-editor' ); ?>
					<input type="hidden" name="action" value="ccfbw_selecting_editor">
					<div class="ccfbw-modal__selecting--rows">
						<div class="ccfbw-modal__selecting--column">
							<input type="radio" name="editor" id="ccfbw-editor-gutenberg" value="gutenberg" class="ccfbw-selecting-editor">
							<label for="ccfbw-editor-gutenberg" class="ccfbw-modal-editor" data-editor="gutenberg">
								<i class="ccfbw-icon-gutenberg"></i>
								<span class="ccfbw-modal-editor__name">
									<?php esc_html_e( 'Gutenberg', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
								</span>
							</label>
						</div>
						<div class="ccfbw-modal__selecting--column">
							<input type="radio" name="editor" id="ccfbw-editor-elementor" value="elementor" class="ccfbw-selecting-editor">
							<label for="ccfbw-editor-elementor" class="ccfbw-modal-editor" data-editor="elementor">
								<i class="ccfbw-icon-elementor"></i>
								<span class="ccfbw-modal-editor__name">
									<?php esc_html_e( 'Elementor', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
								</span>
							</label>
						</div>
					</div>
					<div class="ccfbw-modal__selecting--message"></div>
				</form>
			</div>
		</div>
	</div>
</div>
