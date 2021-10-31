<?php
namespace Kindred\USGS;

use Kindred\USGS\Admin\Admin;
use Kindred\USGS\Request\Request;
use Kindred\USGS\Shortcode\Shortcode;

final class Core {

	const VERSION     = '21.05.01';
	const PLUGIN_NAME = 'usgs-stream-flow-data';

	private static $instance;

	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init( $file ) {
		$request   = new Request();
		$admin     = new Admin( $request );
		$shortcode = new Shortcode( $request );

		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_public_scripts' ] );
		add_action( 'wp_ajax_kwcusgsajax', [ $admin, 'kwcusgsajax_callback' ] );
		add_action( 'admin_menu', [ $admin, 'add_plugin_admin_menu' ] );

		add_filter( 'plugin_action_links_' . plugin_basename( $file ), [ $admin, 'add_action_links' ] );

		add_shortcode( 'USGS', [ $shortcode, 'USGS' ] );
	}

	private function __construct() {
		define( 'USGS_PATH', trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) );
		define( 'USGS_URL', plugin_dir_url( USGS_PATH . self::PLUGIN_NAME ) );
		define( 'USGS_VERSION', self::VERSION );
	}

	private function __clone() {
	}

	public static function activate() {
		return;
	}

	public static function deactivate() {
		return;
	}

	public function register_admin_scripts() {
		$screen = get_current_screen();
		if ( 'settings_page_' . self::PLUGIN_NAME === $screen->id ) {
			wp_enqueue_style( self::PLUGIN_NAME . '-admin-styles', USGS_URL . '/assets/css/admin.css', [], self::VERSION );
			wp_enqueue_script( self::PLUGIN_NAME . '-admin-script', USGS_URL . '/assets/js/admin.js', [ 'jquery' ], self::VERSION, true );
		}
	}

	public function register_public_scripts() {
		wp_enqueue_style( self::PLUGIN_NAME . '-plugin-styles', USGS_URL . '/assets/css/public.css', [], self::VERSION );
	}
}
