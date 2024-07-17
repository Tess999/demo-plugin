<?php
/**
 * Singleton for helpers
 *
 * @package sid
 */

namespace bsp\Components;

/**
 * Class GlobalProperties - Singleton instead $global variable
 *
 * @since 0.0.1
 */
final class GlobalProperties {

	/**
	 * Properties
	 *
	 * @var array
	 */
	private array $props = array();

	/**
	 * Instance
	 *
	 * @var GlobalProperties
	 */
	private static GlobalProperties $instance;

	/**
	 * Get new or exist instance
	 *
	 * @return GlobalProperties
	 */
	public static function get_instance(): GlobalProperties {
		if ( empty( self::$instance ) ) {
			self::$instance = new GlobalProperties();
		}

		return self::$instance;
	}

	/**
	 * Set property
	 *
	 * @param string $key - property name.
	 *
	 * @param mixed  $value - property value.
	 */
	public function set_property( string $key, $value ): void {
		$this->props[ $key ] = $value;
	}

	/**
	 * Get property
	 *
	 * @param string    $key - property name.
	 * @param  bool|null $default_value - property default value.
	 *
	 * @return mixed|null
	 */
	public function get_property( string $key, bool $default_value = null ) {
		return ( $this->props[ $key ] ?? $default_value );
	}
}
