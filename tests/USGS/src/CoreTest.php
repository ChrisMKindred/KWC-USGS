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
	 * @covers Kindred\USGS\Core::__construct
	 */
	public function test_Construct() {
		$this->assertTrue( defined( 'USGS_URL' ) );
		$this->assertTrue( defined( 'USGS_PATH' ) );
		$this->assertTrue( defined( 'USGS_VERSION' ) );
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
		wp_dequeue_style( Core::PLUGIN_NAME . '-admin-style' );

		set_current_screen( 'dashboard' );
		$this->plugin->register_admin_scripts();
		$this->assertNotContains( Core::PLUGIN_NAME . '-admin-script', $wp_scripts->queue );
		$this->assertContains( Core::PLUGIN_NAME . '-admin-styles', $wp_styles->queue );

	}

	/**
	 * Tests register admin scripts
	 *
	 * @covers Kindred\USGS\Core::register_public_scripts
	 */
	public function tests_register_public_scripts() {		{
			$this->markTestIncomplete( 'This test has not been implemented yet.' );
		}
	}
}
