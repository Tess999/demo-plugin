<?php
/**
 * JsonPlaceholder model
 *
 * @package sid
 */

namespace sid\Models;

use sid\Components\Condition;
use sid\Classes\DBTable;

/**
 * Class JsonPlaceHolder
 *
 * @since 0.0.1
 */
class JsonPlaceholderModel extends Model {

	/**
	 * ID
	 *
	 * @var int
	 */
	protected int $id = 0;

	/**
	 * User ID
	 *
	 * @var int
	 */
	protected int $user_id = 0;

	/**
	 * Title
	 *
	 * @var string
	 */
	protected string $task_name = '';

	/**
	 * Is complete flag
	 *
	 * @var bool
	 */
	protected bool $is_complete;

	/**
	 * Errors
	 *
	 * @var array
	 */
	protected array $error_log = array();

	/**
	 * Returns table name
	 *
	 * @return string
	 */
	public static function get_table_name(): string {
		return DBTable::TABLE_NAME;
	}

	/**
	 * Returns record ID
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Returns user ID
	 *
	 * @return int
	 */
	public function get_user_id(): int {
		return $this->user_id;
	}

	/**
	 * Returns task name
	 *
	 * @return string
	 */
	public function get_task_name(): string {
		return $this->task_name;
	}

	/**
	 * Check task is complete
	 *
	 * @return bool
	 */
	public function is_complete(): bool {
		return $this->is_complete;
	}

	/**
	 * Set values from array
	 *
	 * @param array $values - values.
	 *
	 * @return void
	 * @throws Exception - exception.
	 */
	protected function set_values( array $values ): void {
		$this->set_id( intval( sanitize_text_field( wp_unslash( $values['id'] ) ) ) );
		$this->set_user_id( intval( sanitize_text_field( wp_unslash( $values['user_id'] ) ) ) );
		$this->set_task_name( sanitize_text_field( wp_unslash( ( $values['title'] ) ) ) );
		$this->set_is_complete( strcasecmp( sanitize_text_field( wp_unslash( ( $values['title'] ) ) ), 'true' ) === 0 );
	}

	/**
	 * Set ID
	 *
	 * @param int $id - ID.
	 *
	 * @return void
	 * @throws Exception - exception.
	 */
	public function set_id( int $id ): void {
		if ( $id < 1 ) {
			throw new Exception( 'JsonPlaceholder set ID less that 1' );
		}

		$this->id = $id;
	}

	/**
	 * Set user ID
	 *
	 * @param int $user_id - User ID.
	 *
	 * @return void
	 * @throws Exception - exception.
	 */
	public function set_user_id( int $user_id ): void {
		if ( $user_id < 1 ) {
			throw new Exception( 'JsonPlaceholder set ID less that 1' );
		}

		$this->user_id = $user_id;
	}

	/**
	 * Set task name
	 *
	 * @param string $task_name - task name.
	 *
	 * @return void
	 */
	public function set_task_name( string $task_name ): void {
		$this->task_name = sanitize_text_field( wp_unslash( $task_name ) );
	}

	/**
	 * Set "is task complete" flag
	 *
	 * @param bool $is_complete - "is complete" flag.
	 *
	 * @return void
	 */
	protected function set_is_complete( bool $is_complete ): void {
		$this->is_complete = $is_complete;
	}

	/**
	 * Validate instance
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		$this->error_log = array();

		if ( $this->get_id() < 1 ) {
			$this->error_log[] = 'Record ID is must be > 0';
		}

		if ( $this->get_user_id() < 1 ) {
			$this->error_log[] = 'User ID is must be > 0';
		}

		if ( ! $this->get_task_name() ) {
			$this->error_log[] = 'Task name is empty';
		}

		if ( empty( $this->is_complete ) ) {
			$this->error_log[] = '"Is complete" not set';
		}

		if ( count( $this->error_log ) > 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Save or update record
	 *
	 * @return bool
	 */
	public function save(): bool {
		if ( ! $this->is_valid() ) {
			return false;
		}

		try {
			if ( $this->is_new() ) {
				$this->create();
			} else {
				$this->update();
			}

			return true;
		} catch ( Throwable $tw ) {
			$this->error_log[] = $tw->getMessage();

			return false;
		}
	}

	/**
	 * Create new record
	 *
	 * @return bool
	 */
	protected function create(): bool {
		global $wpdb;
		$result = $wpdb->insert(
			$wpdb->prefix . self::get_table_name(),
			array(
				'id'        => $this->get_id(),
				'user_id'   => $this->get_user_id(),
				'title'     => $this->get_task_name(),
				'completed' => $this->is_complete ? 1 : 0,
			),
			array(
				Condition::FORMAT_NUMBER,
				Condition::FORMAT_NUMBER,
				Condition::FORMAT_STRING,
				Condition::FORMAT_NUMBER,
			)
		);

		if ( ! $result ) {
			$this->error_log[] = $wpdb->last_error;
			return false;
		}
		$this->id = $wpdb->insert_id;
		$this->set_is_new( false );

		return true;
	}

	/**
	 * Update exist record
	 * TODO: Do we need use it?
	 *
	 * @return bool
	 */
	protected function update(): bool {
		global $wpdb;
		$result = $wpdb->update(
			$wpdb->prefix . self::get_table_name(),
			array(
				'user_id'   => $this->get_user_id(),
				'title'     => $this->get_task_name(),
				'completed' => $this->is_complete ? 1 : 0,
			),
			array(
				'id' => $this->get_id(),
			),
			array(
				Condition::FORMAT_NUMBER,
				Condition::FORMAT_STRING,
				Condition::FORMAT_NUMBER,
			),
			array( Condition::FORMAT_NUMBER )
		);

		if ( ! $result ) {
			$this->error_log[] = $wpdb->last_error;

			return false;
		}

		return true;
	}
}
