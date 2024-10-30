<?php
/**
 * Template for displaying side list of fields
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Admin\Field;
use CCFBW\Woocommerce\Checkout\Builder\Admin\Fields;
use CCFBW\Woocommerce\Checkout\Builder\Templates;

$_fields = Fields::get();
?>
<div class="ccfbw-sidebar">
	<div class="ccfbw-sidebar__inside" id="ccfbw-scroll-bar">
		<div class="ccfbw-sidebar__title">
			<?php esc_html_e( 'Elements', 'checkout-custom-fields-builder-for-woocommerce' ); ?>
		</div>
		<div class="ccfbw-sidebar__list">
			<div class="ccfbw-sidebar__group ccfbw-drag-section dragster-region--drag-only">
				<?php
				foreach ( $_fields as $field ) :
					$_field = new Field( $field );

					if ( in_array( $_field->get_type(), array( 'country', 'state', 'password' ), true ) ) {
						continue;
					}

					Templates::get_settings_template(
						'components/field',
						true,
						array(
							'field'   => $_field,
							'preview' => true,
						)
					);
				endforeach;
				?>
			</div>
		</div>
	</div>
</div>
