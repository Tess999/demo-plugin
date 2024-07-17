<?php
/**
 * Table for tasks shortcode
 *
 * @var $variables array{
 *     tasks:JsonPlaceholderModel[],
 *     error_message: string,
 * }
 *
 * @package sid
 */

use sid\Models\JsonPlaceholderModel;

if ( $variables['error_message'] ) { ?>
	<div class="error">
		<?php echo esc_html( $variables['error_message'] ); ?>
	</div>
	<?php
}

if ( $variables['tasks'] ) {
	?>

	<table class="table">
		<thead>
		<tr>
			<th><?php esc_html_e( 'ID', 'sid' ); ?></th>
			<th><?php esc_html_e( 'User ID', 'sid' ); ?></th>
			<th><?php esc_html_e( 'Task Name', 'sid' ); ?></th>
			<th><?php esc_html_e( 'Completed', 'sid' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $variables['tasks'] as $task ) { ?>
			<tr>
				<td><?php echo esc_html( $task->get_id() ); ?></td>
				<td><?php echo esc_html( $task->get_user_id() ); ?></td>
				<td><?php echo esc_html( $task->get_task_name() ); ?></td>
				<td><?php $task->is_complete() ? esc_html_e( 'Completed', 'sid' ) : esc_html_e( 'Not Completed', 'sid' ); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

<?php } else { ?>
	<div>
		<?php esc_html_e( 'Tasks_not_found', 'sid' ); ?>
	</div>
	<?php
}
