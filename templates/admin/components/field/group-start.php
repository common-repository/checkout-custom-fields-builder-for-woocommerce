<?php
/**
 * Accordion edit panel start
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$group   = $args['group'];
$counter = $args['counter'];

use CCFBW\Woocommerce\Checkout\Builder\Admin\Fields;
?>
<div class="ccfbw-field-group" data-idx="<?php echo esc_attr( $counter ); ?>">
	<div class="ccfbw-field-group__heading">
		<h3><?php echo esc_html( Fields::get_group_name( $group ) ); ?></h3>
		<button class="ccfbw-field-group__button">
			<i class="ccfbw-icon-arrow-up"></i>
		</button>
	</div>
	<div class="ccfbw-field-group__content" style="display: none;">
