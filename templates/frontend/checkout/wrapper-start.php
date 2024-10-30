<?php
/**
 * Woocommerce checkout before form template
 *
 * Elementor settings
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$settings = $args['settings'];
?>
<div class="ccfbw-form-container">
	<?php if ( $settings['form_header_switch'] ) : ?>
		<div class="ccfbw-form-header">
			<div class="ccfbw-form-header__title"><?php echo esc_html( $settings['form_header_title'] ); ?></div>
			<div class="ccfbw-form-header__subtitle"><?php echo esc_html( $settings['form_header_subtitle'] ); ?></div>
		</div>
	<?php endif; ?>
	<div class="ccfbw-form-row">
		<div class="ccfbw-form-column ccfbw-form-left-column">
