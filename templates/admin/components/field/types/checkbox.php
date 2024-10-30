<?php
/**
 * Text Field
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field_key   = $args['field_key'];
$field_data  = $args['field_data'];
$label       = $field_data['label'] ?? '';
$description = $field_data['description'] ?? '';

if ( ! empty( $field_data['required'] ) ) {
	$label .= '&nbsp;<span class="required">*</span>';
}
?>

<div class="ccfbw-field ccfbw-field__switcher">
	<label class="ccfbw-field-label">
		<span class="ccfbw-field-switcher">
			<input
				type="checkbox"
				name="<?php echo esc_attr( $field_key ); ?>"
				class="ccfbw-switcher">
			<span class="ccfbw-field-switcher__toggle">
				<span class="ccfbw-field-switcher__thumb"></span>
			</span>
		</span>
		<span class="ccfbw-field-switcher__label"><?php echo wp_kses_post( $label ); ?></span>
	</label>
</div>
