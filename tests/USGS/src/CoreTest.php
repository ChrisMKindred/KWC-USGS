<?php
/**
 * Class CoreTests
 *
 * @package Plugin_Test
 */

use Kindred\USGS\Core;

/**
 * Sample test case.
 *
 * @covers Kindred\USGS\Core
 */
class CoreTest extends WP_UnitTestCase {
	protected $plugin;

	/**
	 * @covers Kindred\USGS\Core
	 */
	public function setUp(){
		parent::setUp();
		$this->plugin = Core::instance();
	}

	/**
	 * @covers Kindred\USGS\Core::instance
	 */
	public function test_instance(){
		$this->assertInstanceOf( 'Kindred\USGS\Core', Core::instance() );
	}

	/**
	 * Tests verify the construct set the defines.
	 *
	 * @covers Kindred\USGS\Core
	 */
	public function test_Construct() {
		$this->assertTrue( defined( 'USGS_URL' ) );
		$this->assertTrue( defined( 'USGS_PATH' ) );
		$this->assertTrue( defined( 'USGS_VERSION' ) );
		// $this->assertEquals( USGS_VERSION, Core::VERSION );
	}

	/**
	 * Tests for plugin slug.
	 *
	 * @covers Kindred\USGS\Core::init
	 */
	public function test_init() {
		$file = USGS_PATH . '/kwcusgs.php';
		$this->plugin->init( $file );
		$this->assertTrue( shortcode_exists( 'USGS' ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->plugin, 'register_admin_scripts' ] ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', [ $this->plugin, 'register_public_scripts' ] ) );
	}

	/**
	 * Tests activate
	 *
	 * @covers Kindred\USGS\Core::activate
	 */
	public function tests_activate() {
		$this->assertNull( $this->plugin->activate() );
	}

	/**
	 * Tests deactivate
	 *
	 * @covers Kindred\USGS\Core::deactivate
	 */
	public function tests_deactivate() {
		$this->assertNull( $this->plugin->deactivate() );
	}

	/**
	 * Tests register admin scripts
	 *
	 * @covers Kindred\USGS\Core::register_admin_scripts
	 */
	public function tests_register_admin_scripts() {
		global $wp_scripts;
		global $wp_styles;

		set_current_screen( 'settings_page_' . Core::PLUGIN_NAME );
		$this->plugin->register_admin_scripts();
		$this->assertContains( Core::PLUGIN_NAME . '-admin-script', $wp_scripts->queue );
		$this->assertContains( Core::PLUGIN_NAME . '-admin-styles', $wp_styles->queue );

		wp_dequeue_script( Core::PLUGIN_NAME . '-admin-script' );
		wp_dequeue_style( Core::PLUGIN_NAME . '-admin-styles' );

		set_current_screen( 'dashboard' );
		$this->plugin->register_admin_scripts();
		$this->assertNotContains( Core::PLUGIN_NAME . '-admin-script', $wp_scripts->queue );
		$this->assertNotContains( Core::PLUGIN_NAME . '-admin-styles', $wp_styles->queue );

		wp_dequeue_script( Core::PLUGIN_NAME . '-admin-script' );
		wp_dequeue_style( Core::PLUGIN_NAME . '-admin-styles' );
	}

	/**
	 * Tests register admin scripts
	 *
	 * @covers Kindred\USGS\Core::register_public_scripts
	 */
	public function tests_register_public_scripts() {
		global $wp_styles;
		$this->assertNotContains( Core::PLUGIN_NAME . '-plugin-styles', $wp_styles->queue );
		$this->plugin->register_public_scripts();
		$this->assertContains( Core::PLUGIN_NAME . '-plugin-styles', $wp_styles->queue );
		wp_dequeue_style( Core::PLUGIN_NAME . '-plugin-style' );
	}
}
