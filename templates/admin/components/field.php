<?php
/**
 * The template is used to display a field in the field list
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field    = $args['field'];
$_preview = $args['preview'];

use CCFBW\Woocommerce\Checkout\Builder\Admin\Field;
use CCFBW\Woocommerce\Checkout\Builder\Templates;

$classes = array(
	'ccfbw-field__item',
	'ccfbw-dragster-item',
);

$field = new Field( $field );

if ( $field->is_pro ) {
	$classes[] = 'disabled';
}

if ( ! $field->visible ) {
	$classes[] = 'not-visible';
}

$classes_inline = implode( ' ', $classes );
?>
<div
	class="<?php echo esc_attr( $classes_inline ); ?>"
	data-type="<?php echo esc_attr( $field->get_type() ); ?>"
	data-id="<?php echo esc_attr( $field->id ); ?>">
	<div class="ccfbw-field__item--left">
		<div class="ccfbw-field__item-box">
			<?php if ( $_preview ) : ?>
				<div class="ccfbw-field__item-icon <?php echo esc_attr( $field->icon ); ?>" data-ccfbw-action="section-remove"></div>
			<?php endif; ?>
			<?php printf( '<span class="label">%s</span>', esc_html( $field->get_label() ) ); ?>
			<abbr
				class="required"
				title="<?php esc_attr_e( 'required', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"
				style="display: <?php echo esc_attr( $field->mark_required() ); ?>"
			>*</abbr>
		</div>
		<div class="ccfbw-field__item-type" data-ccfbw-action="section-show" style="display: <?php echo ( ! $_preview ) ? 'inline-block' : 'none'; ?>">
			<?php echo esc_html( $field->get_label_type() ); ?>
		</div>
	</div>
	<div class="ccfbw-field__item--right">
		<?php
			Templates::get_settings_template(
				'components/field/list-actions',
				true,
				array(
					'field'   => $field,
					'preview' => $_preview,
				)
			);
			?>
	</div>
</div>
