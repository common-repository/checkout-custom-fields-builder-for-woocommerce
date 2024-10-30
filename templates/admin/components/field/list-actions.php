<?php
/**
 * Field actions
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Admin\Field;
use CCFBW\Woocommerce\Checkout\Builder\Admin\Load;

/** @var Field $field */
$field    = $args['field'] ?? '';
$_preview = $args['preview'] ?? false;

if ( empty( $_preview ) ) :
	?>
	<div class="ccfbw-field__item-edit">
		<i class="ccfbw-icon-pencil" aria-label="<?php esc_attr_e( 'Editing a section heading', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"></i>
	</div>
	<div class="ccfbw-field__item-draggable">
		<i class="ccfbw-icon-draggable" aria-label="<?php esc_attr_e( 'Draggable field', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"></i>
	</div>
	<button type="button" class="ccfbw-field__item-trash" <?php echo ( ! empty( $field->woo_field ) ) ? 'disabled' : ''; ?>>
		<i class="ccfbw-icon-trash" aria-label="<?php esc_attr_e( 'Remove field', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"></i>
	</button>
<?php elseif ( ! $field->is_free() && ! Load::pro_exist() ) : ?>
	<div class="ccfbw-mark-pro__field">
		<img
			src="<?php echo esc_url( CCFBW_ASSETS_URL . 'images/crown-small.svg' ); ?>"
			class="ccfbw-pro-feature-icon-small"
			alt="<?php esc_attr_e( 'Pro Feature', 'checkout-custom-fields-builder-for-woocommerce' ); ?>">
	</div>
<?php endif; ?>
