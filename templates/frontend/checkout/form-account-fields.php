<?php
/**
 * Checkout account fields
 *
 * @var array $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var WC_Checkout $checkout */
$checkout = $args['checkout'];

if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) :
	$_section_id    = 'account';
	$_section       = apply_filters( 'ccfbw_get_section', array(), $_section_id );
	$_section_title = ( ! empty( $_section['title'] ) ) ? $_section['title'] : '';
	$_section_desc  = ( ! empty( $settings[ 'section_description_' . $_section_id ] ) ) ? $settings[ 'section_description_' . $_section_id ] : '';
	?>
	<div class="woocommerce-account-fields">
		<?php if ( ! $checkout->is_registration_required() ) : ?>
			<h3><?php echo esc_html( $_section_title ); ?></h3>
			<div class="ccfbw-section-subtitle">
				<?php echo esc_html( $_section_desc ); ?>
			</div>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'checkout-custom-fields-builder-for-woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, apply_filters( 'ccfbw_account_form_field', $field ), $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>
