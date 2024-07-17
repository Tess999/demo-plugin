<?php

namespace sid\Models;

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
	public static function get_data() {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::API_URL );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		$response  = curl_exec( $ch );
		$error     = curl_errno( $ch );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		curl_close( $ch );

		if ( $error > 0 ) {
			throw new Exception( 'CURL Error: ' . $error );
		}

		if ( $http_code !== 200 ) {
			throw new Exception( 'HTTP Code is not 200: ' . $http_code );
		}

		return $response;
	}

}
