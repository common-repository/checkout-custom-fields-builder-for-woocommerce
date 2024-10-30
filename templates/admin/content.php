<?php
/**
 * Fields type
 *
 * @var $_fields
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Admin\Load;
use CCFBW\Woocommerce\Checkout\Builder\Templates;

do_action( 'ccfbw_settings_content_before' );
?>
<div class="wrap">
	<h2>
		<?php echo wp_kses_post( get_admin_page_title() ); ?>
	</h2>

	<div class="ccfbw-row regions">

		<?php Templates::get_settings_template( 'sidebar', true ); ?>

		<div class="ccfbw-content">
			<form id="ccfbw-settings" method="POST">
				<?php
					wp_nonce_field( 'ccfbw_save_settings' );

					Templates::get_settings_template( 'components/actions', true );

					Templates::get_settings_template( 'components/last-edited', true );

					Templates::get_settings_template( 'components/sections', true );
				?>
			</form>
		</div>
	</div>
</div>
<?php
	do_action( 'ccfbw_settings_content_after' );
