<?php
/**
 * Shortcode for tasks
 *
 * @package sid
 */

namespace sid\ShortCodes;

use sid\Components\Condition;
use sid\Components\Render;
use sid\Models\JsonPlaceholderModel;
use Throwable;

/**
 * Class
 *
 * @since 0.0.1
 */
class TasksShortCode {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_shortcode( 'sid_tasks', array( $this, 'show_content' ) );
	}

	/**
	 * Prepare and show content
	 *
	 * @param array $attributes - attributes.
	 *
	 * @return string|null
	 */
	public function show_content( array $attributes ): ?string {
		try {
			$count         = 5;
			$error_message = '';

			if ( isset( $attributes['count'] ) ) {
				$input_count = intval( sanitize_text_field( wp_unslash( $attributes['count'] ) ) );
				if ( $input_count < 1 ) {
					$error_message  = __( 'Count should be greater than 0.', 'sid' );
					$error_message .= ' ' . __( 'Default setting ', 'sid' ) . esc_html( $count );
				} else {
					$count = $input_count;
				}
			}

			$condition = new Condition();
			$condition->set_random( true );
			$condition->set_limit( $count );

			$results = JsonPlaceholderModel::find( $condition );

			return Render::view_partial(
				'shortcodes/tasks-shortcode',
				array(
					'tasks'         => $results,
					'error_message' => $error_message,
				),
			);
		} catch ( Throwable $tw ) {
			set_sid_log( $tw->getMessage(), 'error' );

			return '';
		}
	}
}
