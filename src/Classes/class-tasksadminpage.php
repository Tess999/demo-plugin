<?php
/**
 * Tasks admin page
 *
 * @package sid
 */

namespace sid\Classes;

use sid\Components\Render;
use sid\Components\TasksTable;

/**
 * Class TasksAdminPage
 *
 * @since 0.0.1
 */
class TasksAdminPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_tasks_page' ) );
	}

	/**
	 * Register tasks page on admin menu
	 *
	 * @return void
	 */
	public function register_tasks_page() {
		add_menu_page(
			__( 'Tasks', 'sid' ),
			__( 'Tasks page', 'sid' ),
			'manage_options',
			'tasks-sid',
			array(
				$this,
				'show_page',
			),
			'dashicons-tickets',
			40
		);
	}

	/**
	 * Show page content
	 *
	 * @return void
	 */
	public function show_page(): void {
		$table = new TasksTable();
		$table->prepare_items();

		Render::view(
			'admin/tasks-table',
			array(
				'table' => $table,
			),
		);
	}
}
