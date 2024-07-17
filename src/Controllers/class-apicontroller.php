<?php
/**
 * API Controller
 *
 * The class to handle a request from a user to update data in the database.
 *
 * @package sid
 */

namespace sid\Controllers;

use sid\Models\APIModel;
use sid\Models\JsonPlaceholderModel;
use Throwable;

/**
 * Class ApiController
 *
 * @since 0.0.1
 */
class ApiController {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'check_api_data' ) );
	}

	/**
	 * Update data from API
	 *
	 * Validating the incoming request, querying the external api and passing the request to the model for processing.
	 *
	 * @return void
	 */
	public function check_api_data() {
		if ( ! isset( $_GET['sid_wpnonce'] ) || ! isset( $_GET['sid_update'] ) || ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_GET['sid_wpnonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'sid_api_nonce' ) ) {
			return;
		}

		$sid_update = sanitize_text_field( wp_unslash( $_GET['sid_update'] ) );

		if ( strcasecmp( $sid_update, 'true' ) === 0 ) {
			try {
				$data  = APIModel::get_data();
				$items = json_decode( $data, true );
				if ( is_array( $items ) ) {
					$exists_items = array();
					foreach ( $items as $item ) {
						try {
							$id             = intval( sanitize_text_field( wp_unslash( $item['id'] ) ) );
							$exists_items[] = $id;
							$model          = JsonPlaceholderModel::find_by_id( $id );
							if ( is_null( $model ) ) {
								$model = new JsonPlaceholderModel();
							}
							$model->set_id( intval( sanitize_text_field( wp_unslash( $item['id'] ) ) ) );
							$model->set_user_id( intval( sanitize_text_field( wp_unslash( $item['userId'] ) ) ) );
							$model->set_task_name( sanitize_text_field( wp_unslash( ( $item['title'] ) ) ) );
							$model->set_is_complete( (bool) sanitize_text_field( wp_unslash( ( $item['completed'] ) ) ) );
							$model->save();
						} catch ( Throwable $tw ) {
							// TODO: Add error log.
						}
					}

					// Remove unused elements
					JsonPlaceholderModel::delete_unused_elements( $exists_items );
				}
			} catch ( Throwable $tw ) {
				// TODO: Add error log.
			}
			wp_safe_redirect( remove_query_arg( array( 'sid_update', 'sid_wpnonce' ) ) );
			exit;
		}
	}
}
