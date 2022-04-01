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
	 * @covers Kindred\USGS\Core::instance
	 */
	public function setUp(){
		parent::setUp();
		$this->plugin = Core::instance();
	}

	/**
	 * @covers Kindred\USGS\Core::instance
	 */
	public function test_instance(){
		$this->assertInstanceOf( 'Kindred\USGS\Core', $this->plugin );
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
	public function tests_register_admin_scripts() {		{
			$this->markTestIncomplete( 'This test has not been implemented yet.' );
		}
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
