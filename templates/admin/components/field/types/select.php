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
$options     = $field_data['properties']['options'] ?? array();
$description = $field_data['description'] ?? '';
$multiple    = $field_data['multiple'] ?? false;

if ( ! empty( $field_data['required'] ) ) {
	$label = '&nbsp;<span class="required">*</span>';
}
?>

<div class="ccfbw-field ccfbw-field__select">
	<label class="ccfbw-field-label">
		<?php echo esc_html( $label ); ?>
		<select
			placeholder="<?php echo esc_attr( $placeholder ); ?>"
			name="<?php echo esc_attr( $field_key ); ?>"
			<?php echo $multiple ? 'multiple' : ''; ?>
			class="ccfbw-select">
			<?php if ( ! $multiple && 'position' !== $field_key ) : ?>
				<option value=""><?php esc_html_e( 'Not selected', 'checkout-custom-fields-builder-for-woocommerce' ); ?></option>
			<?php endif; ?>
			<?php foreach ( $options as $option ) : ?>
				<option value="<?php echo esc_attr( $option['value'] ); ?>"><?php echo esc_html( $option['label'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</label>
	<?php if ( ! empty( $description ) ) : ?>
		<div class="ccfbw-field-description"><?php echo esc_html( $description ); ?></div>
	<?php endif; ?>
</div>
