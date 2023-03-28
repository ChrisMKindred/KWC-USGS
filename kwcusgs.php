<?php
/**
 * Plugin Name:       USGS Steam Flow Data
 * Plugin URI:        //wordpress.org/plugins/usgs-stream-flow-data/
 * Description:       USGS Stream Flow Data
 * Version:           23.03.01
 * Author:            Chris Kindred
 * Author URI:        //www.kindredwebconsulting.com
 * Text Domain:       kwc_usgs
 * License:           GPL-2.0+
 * License URI:       //www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Requires at least: 5.5
 * Requires PHP:      7.0
 */

use Kindred\USGS\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( 'vendor/autoload.php' );

register_activation_hook( __FILE__, [ Core::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Core::class, 'deactivate' ] );

add_action( 'plugins_loaded', static function () {
	usgs_core()->init( __file__ );
} );

/**
 * Returns the core plugin class instance.
 *
 * @return Core
 */

function usgs_core() {
	return Core::instance();
}
