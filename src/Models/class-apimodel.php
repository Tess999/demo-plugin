<?php
/**
 * Api Model
 *
 * @since 0.0.1
 */

namespace sid\Models;

use Exception;

/**
 * Class APIModel
 *
 * @since 0.0.1
 */
class APIModel {

	const API_URL = 'https://jsonplaceholder.typicode.com/todos';

	/**
	 * Returns data from external resource
	 *
	 * @throws Exception - exception.
	 */
	public static function get_data(): string {
		$response = wp_remote_get( self::API_URL );
		if ( is_wp_error( $response ) ) {
			throw new Exception( 'HTTP Request Error: ' . esc_html( $response->get_error_message() ) );
		}

		$http_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code ) {
			throw new Exception( 'HTTP Code is not 200: ' . esc_html( $http_code ) );
		}

		return wp_remote_retrieve_body( $response );
	}
}
