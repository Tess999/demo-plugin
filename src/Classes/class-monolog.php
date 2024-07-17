<?php
/**
 * Logger
 *
 * @package sid
 */

namespace sid\Classes;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use sid\Components\Render;

/**
 * Logger class
 *
 * @since 0.0.1
 */
class Monolog {

	/**
	 * Constructor
	 */
	public function __construct() {
		$log_path = $this->create_sid_log_directory();
		if ( ! $log_path ) {
			if ( ! session_id() ) {
				session_start();
			}
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			$_SESSION['sid_log_error'] = esc_html__( '<strong>Demo Plugin</strong>: Unable to create log file.', 'sid' );
			return;
		}

		$logger = new Logger( 'sid_logger' );
		$logger->pushHandler( new StreamHandler( $this->create_sid_log_directory() . '/app.log', Level::Debug ) );
		$logger->pushHandler( new FirePHPHandler() );

		gprop()->set_property( 'logger', $logger );
	}

	/**
	 * Show an administrator warning if a log file has not been created
	 *
	 * @return void
	 */
	public function admin_notices() {
		if ( isset( $_SESSION['sid_log_error'] ) ) {
			$message = wp_kses( $_SESSION['sid_log_error'], array( 'strong' => array() ) );
			unset( $_SESSION['sid_log_error'] );

			Render::view(
				'admin/plugin-notice',
				array(
					'type'    => 'error',
					'message' => $message,
				),
			);
		}
	}

	/**
	 * Returns path to log directory
	 *
	 * @return false|string
	 */
	protected function create_sid_log_directory() {
		$upload_dir  = wp_upload_dir();
		$upload_path = $upload_dir['basedir'];
		$sid_log_dir = $upload_path . '/sid-log';

		if ( ! file_exists( $sid_log_dir ) ) {
			if ( wp_mkdir_p( $sid_log_dir ) ) {
				return $sid_log_dir;
			} else {
				return false;
			}
		}

		return $sid_log_dir;
	}
}
