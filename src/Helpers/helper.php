<?php
/**
 * Common helpers
 */

use bsp\Components\GlobalProperties;
use Monolog\Logger;

if ( ! function_exists( 'gprop' ) ) {

	/**
	 * Returns GlobalProperty instance
	 *
	 * @return GlobalProperties
	 */
	function gprop(): GlobalProperties {
		return GlobalProperties::get_instance();
	}
}

if ( ! function_exists( 'set_sid_log' ) ) {

	/**
	 * Set monolog log
	 *
	 * @param string $message - log message.
	 * @param string $level - log level.
	 *
	 * @return void
	 */
	function set_sid_log( string $message, string $level ): void {
		try {
			$logger = gprop()->get_property( 'logger' );
			if ( ! $logger instanceof Logger ) {
				error_log( $message );

				return;
			}

			switch ( $level ) {
				case 'info':
					$logger->info( esc_html( $message ) );
					break;
				case 'notice':
					$logger->notice( esc_html( $message ) );
					break;
				case 'warning':
					$logger->warning( esc_html( $message ) );
					break;
				case 'error':
					$logger->error( esc_html( $message ) );
					break;
				case 'critical':
					$logger->critical( esc_html( $message ) );
					break;
				case 'alert':
					$logger->alert( esc_html( $message ) );
					break;
				case 'emergency':
					$logger->emergency( esc_html( $message ) );
					break;
				case 'debug':
				default:
					$logger->debug( esc_html( $message ) );
					break;
			}
		} catch ( Throwable $tw ) {
			error_log( 'SID log level: ' . $level . '. SID Log message: ' . $message . '. Log system error: ' . $tw->getMessage() );
		}
	}
}
