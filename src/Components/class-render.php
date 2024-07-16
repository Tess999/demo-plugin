<?php
/**
 * Render component
 *
 * @package sid
 */

namespace sid\Components;

/**
 * Class Render
 *
 * @since 0.0.1
 */
class Render {

	/**
	 * Path to the plugin templates
	 *
	 * @var string
	 */
	protected static string $template_folder = 'templates/';

	/**
	 * Echo view file with variables
	 *
	 * @param string $file - path to template.
	 * @param array  $variables - variables sent to the template.
	 */
	public static function view( string $file, array $variables = array() ) {
		echo self::view_partial( $file, $variables );
	}

	/**
	 * Return view file with variables as string
	 *
	 * @param string $file - path to template.
	 * @param array  $variables - variables sent to the template.
	 *
	 * @return false|string
	 */
	public static function view_partial( string $file, array $variables = array() ) {
		ob_start();

		$template = SID_DIR . self::$template_folder . $file;
		$file_end = substr( $template, - 4 );
		if ( strcasecmp( $file_end, '.php' ) !== 0 ) {
			$template .= '.php';
		}
		if ( file_exists( $template ) ) {
			include $template;
		}

		return ob_get_clean();
	}
}
