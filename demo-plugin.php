<?php
/**
 * Plugin Name: Demo Plugin
 * Description: Demo plugin for CV
 * Author: Sergei Iskakov
 * Author URI:  https://www.linkedin.com/in/iskakov-sergey-93099825/
 * Version: 0.0.1
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

use sid\Classes\PluginSetup;

require __DIR__ . '/vendor/autoload.php';

define( 'SID_DIR', plugin_dir_path( __FILE__ ) );
define( 'SID_URL', plugin_dir_url( __FILE__ ) );

new PluginSetup();
