<?php
use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

class CCFBW_Blocks_Integration implements IntegrationInterface {

	const ASSETS_BLOCKS_PATH = CCFBW_ASSETS_URL . 'blocks';

	const LANGUAGES_PATH = CCFBW_PATH . '/languages';

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'ccfbw_checkout_block';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {
		$this->register_contact_block_frontend_scripts();
		$this->register_billing_block_frontend_scripts();
		$this->register_shipping_block_frontend_scripts();
		$this->register_shipping_methods_block_frontend_scripts();

		$this->register_main_integration();
	}

	/**
	 * Registers the main JS file required to add filters and Slot/Fills.
	 */
	public function register_main_integration() {
		$script_path = '/build/index.js';
		$style_path  = '/build/style-index.css';

		$script_asset_path = CCFBW_PATH . '/build/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_path ),
			);

		wp_enqueue_style(
			'ccfbw-blocks-integration',
			self::ASSETS_BLOCKS_PATH . $style_path,
			array(),
			$this->get_file_version( $style_path )
		);

		wp_register_script(
			'ccfbw-blocks-integration',
			self::ASSETS_BLOCKS_PATH . $script_path,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'ccfbw-blocks-integration',
			'checkout-custom-fields-builder-for-woocommerce',
			self::LANGUAGES_PATH
		);

		do_action( 'ccfbw_blocks_integration_register_scripts' );
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return apply_filters(
			'ccfbw_blocks_integration_script_handles',
			array(
				'ccfbw-blocks-integration',
				'ccfbw-contact-information-block-frontend',
				'ccfbw-billing-address-block-frontend',
				'ccfbw-shipping-address-block-frontend',
				'ccfbw-shipping-methods-block-frontend',
			)
		);
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return apply_filters(
			'ccfbw_blocks_integration_editor_script_handles',
			array(
				'ccfbw-blocks-integration',
				'ccfbw-contact-information-block-frontend',
				'ccfbw-billing-address-block-frontend',
				'ccfbw-shipping-address-block-frontend',
				'ccfbw-shipping-methods-block-frontend',
			)
		);
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		$data = array(
			'is_active' => true,
			'fields'    => apply_filters( 'ccfbw_get_fields', array(), 'checkout-form' ),
		);

		return apply_filters( 'ccfbw_checkout_block_script_data', $data );
	}

	public function register_contact_block_frontend_scripts() {
		$script_path  = '/build/ccfbw-contact-information-block-frontend.js';
		$script_asset = $this->get_version_frontend_assets();

		wp_register_script(
			'ccfbw-contact-information-block-frontend',
			self::ASSETS_BLOCKS_PATH . $script_path,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'ccfbw-contact-information-block-frontend', // script handle
			'checkout-custom-fields-builder-for-woocommerce', // text domain
			dirname( __FILE__ ) . '/languages'
		);
	}

	public function register_billing_block_frontend_scripts() {
		$script_path  = '/build/ccfbw-billing-address-block-frontend.js';
		$script_asset = $this->get_version_frontend_assets();

		wp_register_script(
			'ccfbw-billing-address-block-frontend',
			self::ASSETS_BLOCKS_PATH . $script_path,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'ccfbw-billing-address-block-frontend', // script handle
			'checkout-custom-fields-builder-for-woocommerce', // text domain
			dirname( __FILE__ ) . '/languages'
		);
	}

	public function register_shipping_block_frontend_scripts() {
		$script_path  = '/build/ccfbw-shipping-address-block-frontend.js';
		$script_asset = $this->get_version_frontend_assets();

		wp_register_script(
			'ccfbw-shipping-address-block-frontend',
			self::ASSETS_BLOCKS_PATH . $script_path,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'ccfbw-shipping-address-block-frontend', // script handle
			'checkout-custom-fields-builder-for-woocommerce', // text domain
			dirname( __FILE__ ) . '/languages'
		);
	}

	public function register_shipping_methods_block_frontend_scripts() {
		$script_path  = '/build/ccfbw-shipping-methods-block-frontend.js';
		$script_asset = $this->get_version_frontend_assets();

		wp_register_script(
			'ccfbw-shipping-methods-block-frontend',
			self::ASSETS_BLOCKS_PATH . $script_path,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_set_script_translations(
			'ccfbw-shipping-methods-block-frontend', // script handle
			'checkout-custom-fields-builder-for-woocommerce', // text domain
			dirname( __FILE__ ) . '/languages'
		);
	}

	protected function get_version_frontend_assets() {
		$script_asset_path = self::ASSETS_BLOCKS_PATH . '/build/checkout-block-frontend.asset.php';
		return file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return CCFBW_VERSION;
	}
}
