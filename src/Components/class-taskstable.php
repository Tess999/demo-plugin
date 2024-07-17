<?php
/**
 * Tasks table
 *
 * @package sid
 */

namespace sid\Components;

use sid\Models\JsonPlaceholderModel;
use WP_List_Table;

/**
 * Class TasksTable
 *
 * @since 0.0.1
 */
class TasksTable extends WP_List_Table {

	const ITEMS_PER_PAGE = 20;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'tasks_link',
				'plural'   => 'tasks_links',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Add refresh API data button
	 *
	 * @param string $which - position.
	 *
	 * @return void
	 */
	public function extra_tablenav( $which ): void {
		if ( 'top' === $which ) {
			$address = '/';
			if ( isset( $_SERVER['REQUEST_URI'] ) ) {
				$address = sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) );
			}

			$refresh_url = add_query_arg(
				array(
					'sid_update'  => 'true',
					'sid_wpnonce' => wp_create_nonce( 'sid_api_nonce' ),
				),
				$address
			);
			echo '<a href="' . esc_url( $refresh_url ) . '">' . esc_html__( 'Refresh', 'sid' ) . '</a>';
		}
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
	 * @param array  $item - item.
	 * @param string $column_name - column name.
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
				return esc_html( 'undefined' );
		}
	}

	/**
	 * Prepare items for table
	 *
	 * TODO: Search adds unnecessary data to the URL
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		global $_wp_column_headers;
		$screen = get_current_screen();

		$current_page = $this->get_pagenum();
		$per_page     = self::ITEMS_PER_PAGE;
		$offset       = ( $current_page - 1 ) * $per_page;
		$order_by     = $this->get_sort_by_default( $sortable );
		$search       = '';

		$order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : $order_by;
		$order    = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';

		// Search.
		if ( isset( $_REQUEST['s'] ) ) {
			if ( isset( $_REQUEST['tasks_search_nonce_field'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['tasks_search_nonce_field'] ) );
				if ( wp_verify_nonce( $nonce, 'tasks_search_nonce' ) ) {
					$search = ! empty( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
				}
			}
		}

		$condition = new Condition();
		$condition->set_limit( $per_page );
		$condition->set_offset( $offset );
		$condition->set_order( $order_by, $order );

		if ( $search ) {
			$condition->set_where_like( 'title', '%' . $search . '%', Condition::FORMAT_STRING );

			// TODO: Additional call to retrieve all records. Need to find a better way.
			$total_items = count( JsonPlaceholderModel::find( $condition ) );
		} else {
			$total_items = count( JsonPlaceholderModel::find_all() );
		}

		$records = JsonPlaceholderModel::find( $condition );

		$columns                           = $this->get_columns();
		$_wp_column_headers[ $screen->id ] = $columns;

		/**
		 * Records loop
		 *
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
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Search column for sorting by default
	 *
	 * @param array $sortable - list of sortable columns.
	 *
	 * @return string
	 */
	public function get_sort_by_default( array $sortable ): string {
		$result = array_key_first( $sortable );
		foreach ( $sortable as $column => $data ) {
			if ( true === $data[1] ) {
				$result = $column;
				break;
			}
		}

		return $result;
	}

	/**
	 * Returns sortable column
	 *
	 * @return array[]
	 */
	public function get_sortable_columns(): array {
		return array(
			'id'        => array( 'id', true ),
			'user_id'   => array( 'user_id', false ),
			'title'     => array( 'title', false ),
			'completed' => array( 'completed', false ),
		);
	}

	/**
	 * Returns text for empty table
	 *
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No tasks found.', 'sid' );
	}

	/**
	 * Returns hidden columns
	 *
	 * @return array
	 */
	private function get_hidden_columns(): array {
		return array();
	}
}
