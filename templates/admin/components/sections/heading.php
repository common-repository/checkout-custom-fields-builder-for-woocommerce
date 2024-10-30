<?php
/**
 * Section heading
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section = $args['section'];
$_editor = apply_filters( 'ccfbw_current_editor', false );

$heading_css = 'ccfbw-accordion__title';
if ( 'elementor' === $_editor ) {
	$heading_css .= ' ccfbw-section__title';
}
?>
<div class="ccfbw-accordion__heading">
	<div class="ccfbw-accordion__heading--left">
		<div class="ccfbw-accordion__draggable">
			<i class="ccfbw-icon-draggable" aria-label="<?php esc_attr_e( 'Draggable section', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"></i>
		</div>
		<span class="<?php echo esc_attr( $heading_css ); ?>" id="<?php echo esc_attr( 'section-' . $section['id'] ); ?>">
			<?php echo esc_html( $section['title'] ); ?>
		</span>
		<?php if ( 'elementor' === $_editor ) : ?>
			<div class="ccfbw-accordion__input">
				<div class="ccfbw-accordion__title--edit ccfbw-section__title--edit">
					<i class="ccfbw-icon-pencil" aria-label="<?php esc_attr_e( 'Editing a section heading', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"></i>
				</div>
				<div class="ccfbw-change-section-name">
					<input
						type="text"
						aria-label="<?php esc_attr_e( 'Entering a section name', 'checkout-custom-fields-builder-for-woocommerce' ); ?>"
						value="<?php echo esc_attr( $section['title'] ); ?>"
						aria-labelledby="shipping-info"
						class="ccfbw-input ccfbw-input-section-title"
					>
					<button type="button" class="button button-primary ccfbw-section__save" title="<?php esc_attr_e( 'Save', 'checkout-custom-fields-builder-for-woocommerce' ); ?>">
						<?php esc_html_e( 'Save', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
					</button>
					<button type="button" class="button ccfbw-section__cancel" title="<?php esc_attr_e( 'Cancel', 'checkout-custom-fields-builder-for-woocommerce' ); ?>">
						<?php esc_html_e( 'Cancel', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
					</button>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="ccfbw-accordion__heading--right">
		<button class="ccfbw-accordion__button active">
			<i class="ccfbw-icon-arrow-up"></i>
		</button>
	</div>
</div>
