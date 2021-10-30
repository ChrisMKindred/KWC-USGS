<?php
Namespace Kindred\USGS\Settings;

use Kindred\USGS\Core;

class Settings {

	public function __construct( $file ) {
		add_action( 'admin_menu', [ $this, 'add_plugin_admin_menu' ] );
		$plugin_basename = plugin_basename( $file );
		add_filter( 'plugin_action_links_' . $plugin_basename, [ $this, 'add_action_links' ] );
	}

	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'USGS Stream Flow Data', 'kwc_usgs' ),
			__( 'Stream Flow Data', 'kwc_usgs' ),
			'manage_options',
			Core::PLUGIN_NAME,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	public function add_action_links( $links ) {
		$setting_link = [
			'settings' =>  '<a href="' . admin_url( 'options-general.php?page=' . Core::PLUGIN_NAME ) . '">' . __( 'Settings', 'kwc_usgs' ) . '</a>',
		];
		return array_merge( $setting_link, $links );
	}

	public function display_plugin_admin_page() {
		include_once( USGS_PATH . '/admin/views/admin.php' );
	}
}
