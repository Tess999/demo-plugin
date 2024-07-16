<?php
/**
 * Admin notice view
 *
 * @var $variables array{
 *     type:string,
 *     message:string,
 * }
 *
 * @package sid
 */

?>

<div class="notice notice-<?php echo esc_attr( $variables['type'] ); ?>">
	<p><?php echo wp_kses( $variables['message'], array( 'strong' => array() ) ); ?></p>
</div>
