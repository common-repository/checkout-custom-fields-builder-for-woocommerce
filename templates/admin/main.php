<?php
/**
 * Plugin settings display template
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Templates;

do_action( 'ccfbw_settings_screen_before' );
?>
	<div class="ccfbw-settings">
		<?php Templates::get_settings_template( 'content', true ); ?>
	</div>
	<div class="ccfbw-modal-overlay"></div>
<?php
do_action( 'ccfbw_settings_screen_after' );
