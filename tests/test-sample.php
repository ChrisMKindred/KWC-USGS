<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

use Kindred\USGS\Core;

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {
	protected $plugin;

	public function setUp(){
		parent::setUp();
		$this->plugin = Core::instance();
	}
	/**
	 * Tests for plugin slug.
	 * @covers Kindred\USGS\Core::__construct
	 */
	public function test_defines() {
		$this->assertTrue( defined( 'USGS_URL' ) );
		$this->assertTrue( defined( 'USGS_PATH' ) );
		$this->assertTrue( defined( 'USGS_VERSION' ) );
	}
}
