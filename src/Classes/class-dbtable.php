<?php
/**
 * Install DB Table
 *
 * @package sid
 */

namespace sid\Classes;

/**
 * A class for working with a table.
 *
 * A table is created/deleted in the class, which is necessary for the plugin to work.
 *
 * @since 0.0.1
 */
class DBTable {

	const TABLE_NAME = 'sid_works';

	/**
	 * Create sid_works table
	 *
	 * @return bool
	 */
	public static function create_sid_works_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;
		$table_name      = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = 'CREATE TABLE ' . $table_name . " (
			  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			  user_id BIGINT UNSIGNED NOT NULL,
			  title TEXT NOT NULL,
			  completed TINYINT(1) NOT NULL,
			  PRIMARY KEY (id)
			) $charset_collate ENGINE=InnoDB;";

		dbDelta( $sql );
		if ( $wpdb->last_error ) {
			// TODO: Add to error log.
			return false;
		}

		return true;
	}

	/**
	 * Delete sid_works table
	 *
	 * @return bool
	 */
	public static function delete_sid_works_table(): bool {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;
		$table_name = $wpdb->prefix . self::TABLE_NAME;
		$wpdb->query( 'DROP TABLE ' . esc_sql( $table_name ) );

		if ( $wpdb->last_error ) {
			// TODO: Add to error log.
			return false;
		}

		return true;
	}
}
