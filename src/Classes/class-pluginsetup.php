<?php
/**
 * Class for plugin initialization
 *
 * @package sid
 */

namespace sid\Classes;

use sid\Components\Render;

/**
 * Class PluginSetup
 *
 * @since 0.0.1
 */
class PluginSetup {

	/**
	 * Path to main plugin file.
	 *
	 * @var string
	 */
	public static string $plugin_main_file = 'demo-plugin/demo-plugin.php';

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( ! session_id() ) {
			session_start();
		}

		register_activation_hook(
			self::$plugin_main_file,
			array( $this, 'install_plugin' )
		);

		register_deactivation_hook(
			self::$plugin_main_file,
			array( $this, 'uninstall_plugin' )
		);

		$this->add_actions();
	}

	/**
	 * Add required actions
	 *
	 * @return void
	 */
	public function add_actions(): void {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Adding a table to the database when installing the plugin
	 *
	 * @return void
	 */
	public function install_plugin(): void {
		if ( ! DBTable::create_sid_works_table() ) {
			add_action( 'shutdown', array( $this, 'deactivate_plugin_on_shutdown' ) );
			$_SESSION['sid_plugin_error'] = __( 'Unable to install plugin <strong>DEMO</strong>', 'sid' );
		}
	}

	/**
	 * Deactivate plugin if something went wrong
	 *
	 * @return void
	 */
	public function deactivate_plugin_on_shutdown(): void {
		deactivate_plugins( plugin_basename( self::$plugin_main_file ) );
	}

	/**
	 * Deleting the table when the plugin is deactivated
	 *
	 * @return void
	 */
	public function uninstall_plugin(): void {
		if ( ! DBTable::delete_sid_works_table() ) {
			$_SESSION['sid_plugin_error'] = __( 'Unable to delete table for plugin <strong>DEMO</strong>', 'sid' );
		}
	}

	/**
	 * Show an admin message if there are any issues with plugin activation/deactivation
	 *
	 * TODO: This is not shown when the plugin is deactivated
	 *
	 * @return void
	 */
	public function admin_notices(): void {
		if ( isset( $_SESSION['sid_plugin_error'] ) ) {
			$message = wp_kses( $_SESSION['sid_plugin_error'], array( 'strong' => array() ) );
			unset( $_SESSION['sid_plugin_error'] );

			Render::view(
				'admin/plugin-notice',
				array(
					'type'    => 'error',
					'message' => $message,
				),
			);
		}
	}
}
