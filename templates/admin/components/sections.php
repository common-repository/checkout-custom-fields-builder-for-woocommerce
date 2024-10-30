<?php
/**
 * The template is used to display sections and their fields
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Templates;

$_sections = apply_filters( 'ccfbw_get_settings', array() );
$_editor   = apply_filters( 'ccfbw_current_editor', false );

$wrap_classes = array( 'ccfbw-wrapper-sections' );
if ( 'gutenberg' === $_editor ) {
	$wrap_classes[] = 'ccfbw-editor-gutenberg';
} elseif ( 'elementor' === $_editor ) {
	$wrap_classes[] = 'ccfbw-drag-container';
} else {
	$wrap_classes[] = 'ccfbw-editor-no-selected';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $wrap_classes ) ); ?>">
	<?php
	foreach ( $_sections as $section ) :
		$_fields = $section['fields'];
		?>
		<div class="ccfbw-accordion ccfbw-section ccfbw-dragster-accordion <?php echo ( ! empty( $section['opened'] ) ) ? 'opened' : ''; ?>" data-section="<?php echo esc_attr( $section['id'] ); ?>">
			<?php
			if ( 'elementor' === $_editor ) {
				Templates::get_settings_template(
					'components/sections/heading',
					true,
					array( 'section' => $section )
				);
			}
			?>
			<div class="ccfbw-accordion__content" style="display: <?php echo ( ! empty( $section['opened'] ) ) ? 'block' : 'none'; ?>">
				<div class="ccfbw-drag-section">
					<?php
					if ( ! empty( $_fields ) ) :
						foreach ( $_fields as $_field ) :
							Templates::get_settings_template(
								'components/field',
								true,
								array(
									'field'   => $_field,
									'preview' => false,
								)
							);
						endforeach;
					endif;
					?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>

	<div class="ccfbw-wrapper-bottom__bar">
		<?php
			/**
			 * Hook: ccfbw_after_list_sections.
			 *
			 * @hooked add_new_section_button - 10
			 */
			do_action( 'ccfbw_after_list_sections' );
		?>
	</div>
</div>
