<?php
/**
 * Choice fields - select|checkbox|radio
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field_key         = $args['field_key'];
$field_data        = $args['field_data'];
$labels            = $field_data['labels'] ?? '';
$placeholders      = $field_data['placeholders'] ?? '';
$label_name        = $labels['name'] ?? '';
$label_value       = $labels['value'] ?? '';
$placeholder_name  = $placeholders['name'] ?? '';
$placeholder_value = $placeholders['value'] ?? '';
$classes           = array( 'ccfbw-options-container' );

if ( ! empty( $field_data['type'] ) ) {
	$classes[] = $field_data['type'];
}

$classes_inline = implode( ' ', $classes );
?>
<div class="<?php echo esc_attr( $classes_inline ); ?>">
	<div class="ccfbw-options-header">
		<div class="ccfbw-options-column">
			<?php echo esc_html( $label_name ); ?>
		</div>
		<div class="ccfbw-options-column">
			<?php echo esc_html( $label_value ); ?>
		</div>
	</div>
	<div class="ccfbw-options">
		<div class="ccfbw-options-row">
			<div class="ccfbw-option-draggable">
				<i class="ccfbw-icon-draggable"></i>
			</div>
			<div class="ccfbw-options-column">
				<input type="text" class="ccfbw-input" name="options[label]" placeholder="<?php echo esc_attr( $placeholder_name ); ?>">
			</div>
			<div class="ccfbw-options-column">
				<input type="text" class="ccfbw-input" name="options[value]" placeholder="<?php echo esc_attr( $placeholder_value ); ?>">
			</div>
			<button type="button" class="ccfbw-option-trash" disabled>
				<i class="ccfbw-icon-trash" aria-label="<?php esc_attr_e( 'Remove option', 'checkout-custom-fields-builder-for-woocommerce-pro' ); ?>"></i>
			</button>
		</div>
	</div>
	<div class="ccfbw-options-actions">
		<button type="button" class="button">
			<i class="ccfbw-icon-plus"></i>
			<?php esc_html_e( 'Add new', 'checkout-custom-fields-builder-for-woocommerce-pro' ); ?>
		</button>
	</div>
</div>
