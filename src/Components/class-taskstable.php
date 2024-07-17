<?php
/**
 * Class TasksTable
 *
 * @since 0.0.1
 */

namespace sid\Components;

use sid\Models\JsonPlaceholderModel;
use WP_List_Table;

class TasksTable extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'tasks_link',
			'plural'   => 'tasks_links',
			'ajax'     => false,
		) );
	}

	/**
	 * Returns columns
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return array(
			'id'        => __( 'ID', 'sid' ),
			'user_id'   => __( 'User ID', 'sid' ),
			'title'     => __( 'Task Name', 'sid' ),
			'completed' => __( 'Completed', 'sid' ),
		);
	}

	/**
	 * Returns row values
	 *
	 * @param $item
	 * @param $column_name
	 *
	 * @return bool|mixed|string|void
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return $item['id'];
			case 'user_id':
				return $item['user_id'];
			case 'title':
				return $item['title'];
			case 'completed':
				if ( $item['completed'] ) {
					$color = '#0e7b17';
					$text  = __( 'Completed', 'sid' );
				} else {
					$color = '#8a1c1d';
					$text  = __( 'Not Completed', 'sid' );
				}

				return '<span style="color: ' . $color . '">' . $text . '</span>';
			default:
				return print_r( $item, true );
		}
	}

	/**
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		global $_wp_column_headers;
		$screen = get_current_screen();

		$condition = new Condition();
		$condition->set_limit( 10 );
		$records = JsonPlaceholderModel::find( $condition );


		$columns                           = $this->get_columns();
		$_wp_column_headers[ $screen->id ] = $columns;

		/**
		 * @var JsonPlaceholderModel[] $records
		 */
		foreach ( $records as $record ) {
			$this->items[] = array(
				'id'        => $record->get_id(),
				'user_id'   => $record->get_user_id(),
				'title'     => $record->get_task_name(),
				'completed' => $record->is_complete(),
			);
		}

		$this->_column_headers = array( $columns, $hidden, $sortable );
	}

	/**
	 * @return array
	 */
	private function get_hidden_columns(): array {
		return array();
	}
}
