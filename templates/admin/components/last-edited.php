<?php
/**
 * Template for displaying the date of the last change to settings
 *
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use CCFBW\Woocommerce\Checkout\Builder\Admin\Options;

$_edited_date = Options::get_edited_date( true );
?>
<div class="ccfbw-last-edited">
	<?php echo wp_kses_post( $_edited_date ); ?>
</div>
