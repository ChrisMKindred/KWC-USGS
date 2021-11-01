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

	public function setUp(){
		parent::setUp();
		$this->plugin = Core::instance();
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
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $this->plugin, 'register_admin_scripts' ] ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', [ $this->plugin, 'register_public_scripts' ] ) );
	}
}
