<?php
/**
 * Table for tasks
 *
 * @var $variables array{
 *     table:TasksTable
 * }
 *
 * @package sid
 */

use sid\Components\TasksTable;

?>

<div class="wrap">
	<h2><?php esc_html_e( 'Tasks', 'sid' ); ?></h2>
	<form method="get">
		<input type="hidden" name="page" value="/tasks-sid"/>
		<?php
		$variables['table']->search_box( __( 'Search Tasks', 'sid' ), 'tasks_search' );
		wp_nonce_field( 'tasks_search_nonce', 'tasks_search_nonce_field', false );
		$variables['table']->display();
		?>
	</form>
</div>
