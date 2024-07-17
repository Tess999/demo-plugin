<?php
/**
 * Common model class for custom ORM
 *
 * @package sid
 */

namespace sid\Models;

use sid\Components\Condition;
use Exception;
use mysqli_result;

/**
 * Abstract class Model
 *
 * @since 0.0.1
 */
abstract class Model {

	/**
	 * Is new flag
	 *
	 * @var bool
	 */
	protected bool $is_new = true;

	/**
	 * Returns table name
	 *
	 * @return string
	 */
	abstract public static function get_table_name(): string;

	/**
	 * Set values to model
	 *
	 * @param array $values - values to set.
	 *
	 * @return void
	 */
	abstract protected function set_values( array $values ): void;

	/**
	 * Check if record is new and have not added to DB
	 *
	 * @return bool
	 */
	public function is_new(): bool {
		return $this->is_new;
	}

	/**
	 * Set flag "Is new"
	 *
	 * @param bool $is_new - flag boolean value.
	 *
	 * @return void
	 */
	public function set_is_new( bool $is_new ): void {
		$this->is_new = $is_new;
	}

	/**
	 * Find records by conditions
	 *
	 * @param Condition $condition - condition for search.
	 *
	 * @return array
	 */
	public static function find( Condition $condition ): array {
		global $wpdb;
		$child_class = get_called_class();
		$table_name  = $child_class::get_table_name();

		$result = array();
		$sql    = 'SELECT * FROM ' . $wpdb->prefix . $table_name;

		$prepare = $condition->prepare_condition();
		$sql    .= $prepare['sql'];

		// TODO: esc_sql added slashes
		$sql_result = $wpdb->get_results( $wpdb->prepare( $sql, $prepare['vars'] ), ARRAY_A );
		foreach ( $sql_result as $value ) {
			$model = new $child_class();
			$model->set_values( $value );
			$model->is_new = false;
			$result[]      = $model;
		}

		return $result;
	}

	/**
	 * Delete record from DB
	 *
	 * @param int $id - record ID.
	 *
	 * @return bool|int|mysqli_result|null
	 * @throws Exception - exception.
	 */
	public static function delete( int $id ) {
		global $wpdb;
		$child_class = get_called_class();
		$table_name  = $child_class::get_table_name();
		if ( ! self::is_db_column( 'id' ) ) {
			throw new Exception();
		}
		$sql = 'DELETE FROM `' . $wpdb->prefix . $table_name . '` WHERE id=%d LIMIT 1';

		return $wpdb->query( esc_sql( $wpdb->prepare( $sql, $id ) ) );
	}

	/**
	 * Find record by ID
	 *
	 * @param int $id - record ID.
	 *
	 * @return Model|null
	 * @throws Exception - exception.
	 */
	public static function find_by_id( int $id ): ?Model {
		global $wpdb;
		$child_class = get_called_class();
		$table_name  = $child_class::get_table_name();

		if ( ! self::is_db_column( 'id' ) ) {
			throw new Exception();
		}

		$sql    = 'SELECT * FROM ' . $wpdb->prefix . $table_name . ' WHERE id=%d LIMIT 1';
		$result = $wpdb->get_row( esc_sql( $wpdb->prepare( $sql, $id ) ), ARRAY_A );

		if ( ! is_null( $result ) ) {
			$model = new $child_class();
			$model->set_values( $result );
			$model->is_new = false;

			return $model;
		}

		return null;
	}

	/**
	 * Find all records
	 *
	 * @return array
	 */
	public static function find_all(): array {
		global $wpdb;
		$result      = array();
		$child_class = get_called_class();
		$table_name  = $child_class::get_table_name();

		$sql        = 'SELECT * FROM ' . $wpdb->prefix . $table_name;
		$sql_result = $wpdb->get_results( esc_sql( $sql ), ARRAY_A );

		foreach ( $sql_result as $value ) {
			$model = new $child_class();
			$model->set_values( $value );
			$model->is_new = false;
			$result[]      = $model;

		}

		return $result;
	}

	/**
	 * Check if column exists in table
	 *
	 * @param string $column_name - column name.
	 *
	 * @return bool
	 */
	private static function is_db_column( string $column_name ): bool {
		global $wpdb;
		$child_class = get_called_class();
		$table_name  = $child_class::get_table_name();

		$sql        = "SELECT `COLUMN_NAME` 
							FROM `INFORMATION_SCHEMA`.`COLUMNS` 
							WHERE `TABLE_SCHEMA`='" . $wpdb->dbname . "' 
    						AND `TABLE_NAME`='" . $wpdb->prefix . $table_name . "'";
		$sql_result = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $sql_result as $column ) {
			if ( strcmp( $column['COLUMN_NAME'], $column_name ) === 0 ) {
				return true;
			}
		}

		return false;
	}
}
