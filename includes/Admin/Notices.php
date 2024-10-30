<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Admin;

use CCFBW\Woocommerce\Checkout\Builder\Plugin;

defined( 'ABSPATH' ) || exit;

class Notices {

	/**
	 * Required Plugin.
	 *
	 * @var array
	 */
	public $plugin;

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * Load constructor.
	 *
	 * @param string $plugin_name     Plugin name.
	 * @param array  $required_plugin Required Plugin.
	 */
	public function __construct( string $plugin_name, array $required_plugin = array(), bool $required = false ) {
		$this->plugin_name = $plugin_name;
		$this->plugin      = Plugin::get_instance( $required_plugin['slug'], $required_plugin['name'] );

		if ( $required ) {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
	}

	/**
	 * Add admin notice.
	 *
	 * @return void
	 */
	public function admin_notices() {

		$screen = get_current_screen();

		if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
			return;
		}

		if ( ! ( $this->plugin->get_plugin_slug() || $this->plugin->get_plugin_name() ) ) {
			return;
		}

		$this->add_notice( $this->plugin );
	}

	/**
	 * Add notice.
	 *
	 * @param Plugin $plugin Plugin.
	 *
	 * @return bool
	 */
	private function add_notice( Plugin $plugin ) {

		if ( $plugin->is_plugin_activated() ) {
			return false;
		}

		if ( $plugin->is_plugin_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return false;
			}
			?>
			<div class="error">
				<p>
					<a href="<?php echo esc_url( $plugin->get_plugin_activate_link() ); ?>" class='button button-secondary'><?php printf( esc_html__( 'Activate % s', 'checkout-custom-fields-builder-for-woocommerce' ), esc_html( $plugin->get_plugin_name() ) ); ?></a>
					<?php
						printf(
							/* translators: %1$s plugin name & %2$s required plugin name */
							esc_html__( 'The %1$s is not working because you need to activate the %2$s plugin. ', 'checkout-custom-fields-builder-for-woocommerce' ),
							esc_html( $this->plugin_name ),
							esc_html( $plugin->get_plugin_name() )
						);
					?>
				</p>
			</div>
			<?php
			return true;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}
		?>
		<div class="error">
			<p>
				<a href="<?php echo esc_url( $plugin->get_plugin_install_link() ); ?>" class='button button-secondary'>
					<?php
						printf(
							/* translators: %1$s plugin name & %2$s required plugin name */
							esc_html__( 'Install %s', 'checkout-custom-fields-builder-for-woocommerce' ),
							esc_html( $plugin->get_plugin_name() )
						);
					?>
				</a>
				<?php
					printf(
						/* translators: %1$s plugin name & %2$s required plugin name */
						esc_html__( 'The %1$s is not working because you need to install the %2$s plugin. ', 'checkout-custom-fields-builder-for-woocommerce' ),
						esc_html( $this->plugin_name ),
						esc_html( $plugin->get_plugin_name() )
					);
				?>
			</p>
		</div>
		<?php
		return true;
	}
}
