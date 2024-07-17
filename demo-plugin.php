<?php
/**
 * Plugin Name: Demo API
 * Description: Demo API for CV
 * Author: Sergei Iskakov
 * Author URI:  https://www.linkedin.com/in/iskakov-sergey-93099825/
 * Version: 1.0.0
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package sid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

use sid\Classes\Monolog;
use sid\Classes\PluginSetup;
use sid\Classes\TasksAdminPage;
use sid\Controllers\ApiController;
use sid\ShortCodes\TasksShortCode;

require __DIR__ . '/vendor/autoload.php';

define( 'SID_DIR', plugin_dir_path( __FILE__ ) );
define( 'SID_URL', plugin_dir_url( __FILE__ ) );

new Monolog();
new PluginSetup();
new TasksAdminPage();
new ApiController();
new TasksShortCode();
