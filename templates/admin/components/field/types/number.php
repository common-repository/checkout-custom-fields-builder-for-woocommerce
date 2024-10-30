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
$placeholder = $field_data['placeholder'] ?? '';
$min         = $field_data['min'] ?? '';
$max         = $field_data['max'] ?? '';
$step        = $field_data['step'] ?? '';
$description = $field_data['description'] ?? '';

if ( ! empty( $field_data['required'] ) ) {
	$label .= '&nbsp;<span class="required">*</span>';
}
?>

<div class="ccfbw-field">
	<label class="ccfbw-field-label">
		<?php echo wp_kses_post( $label ); ?>
		<input
			type="number"
			min="<?php echo esc_attr( $min ); ?>"
			max="<?php echo esc_attr( $max ); ?>"
			step="<?php echo esc_attr( $step ); ?>"
			name="<?php echo esc_attr( $field_key ); ?>"
			class="ccfbw-input"
			placeholder="<?php echo esc_attr( $placeholder ); ?>">
	</label>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="ccfbw-field-description"><?php echo esc_html( $description ); ?></div>
	<?php endif; ?>
</div>
