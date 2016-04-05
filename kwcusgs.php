<?php
/**
 *
 *
 * @package   USGS Steam Flow Data
 * @author    Chris Kindred <chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      http://www.kindredwebconsulting.com
 * @copyright 2015 Kindred Web Consulting
 *
 * @wordpress-plugin
 * Plugin Name:       USGS Steam Flow Data
 * Plugin URI:        http://wordpress.org/plugins/usgs-stream-flow-data/
 * Description:       USGS Stream Flow Data
 * Version:           2.4
 * Author:            Chris Kindred
 * Author URI:        http://www.kindredwebconsulting.com
 * Text Domain:       kwcusgs-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once plugin_dir_path( __FILE__ ) . 'public/class-kwcusgs.php';

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'kwc_usgs', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'kwc_usgs', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'kwc_usgs', 'get_instance' ) );
add_filter( 'widget_text', 'do_shortcode' );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() ) {

	require_once plugin_dir_path( __FILE__ ) . 'admin/class-kwcusgs-admin.php';
	add_action( 'plugins_loaded', array( 'kwc_usgs_admin', 'get_instance' ) );

}
