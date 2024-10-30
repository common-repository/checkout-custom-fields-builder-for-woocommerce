<?php
/**
 * Plugin Name: Checkout Custom Fields Builder for WooCommerce
 * Plugin URI: http://stylemixthemes.com/
 * Description: Change the WooCommerce checkout page to fit your needs. Add, remove, and edit fields and make the checkout process simple and personalized for your online store.
 * Author: StylemixThemes
 * Author URI: https://stylemixthemes.com/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /languages
 * Text Domain: checkout-custom-fields-builder-for-woocommerce
 * WC requires at least: 8.8.3
 * WC tested up to: 9.3.3
 * Requires PHP: 7.4
 * Requires at least to: 6.4
 * Tested up to: 6.6
 * Version: 1.1.1
 */

defined( 'ABSPATH' ) || exit;

define( 'CCFBW_PLUGIN_NAME', 'Checkout Custom Fields Builder for WooCommerce' );
define( 'CCFBW_PLUGIN_SLUG', 'checkout-custom-fields-builder-for-woocommerce' );
define( 'CCFBW_VERSION', '1.1.1' );
define( 'CCFBW_FILE', __FILE__ );
define( 'CCFBW_PATH', dirname( CCFBW_FILE ) );
define( 'CCFBW_URL', plugin_dir_url( CCFBW_FILE ) );
define( 'CCFBW_ASSETS_URL', CCFBW_URL . 'assets/' );

require_once CCFBW_PATH . '/vendor/autoload.php';

global $ccfbw_load;

$ccfbw_load = new CCFBW\Woocommerce\Checkout\Builder\Load( CCFBW_PLUGIN_NAME );
