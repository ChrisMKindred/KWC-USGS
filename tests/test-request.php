<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

use Kindred\USGS\Admin\Admin;
use Kindred\USGS\Core;
use Kindred\USGS\Request\Request;

/**
 * Sample test case.
 */
class RequestTest extends WP_UnitTestCase {
	protected $request;

	public function setUp(){
		parent::setUp();
		$this->request = new Request();
	}

	/**
	 * Does the options page actually exists for the admin user.
	 *
	 * @covers Kindred\USGS\Request\Request::get_usgs
	 */
	public function test_bad_request() {
		$this->assertInstanceOf( 'WP_Error', $this->request->get_usgs( 'http://badurl' ) );
	}

	/**
	 * Tests to make sure that the add action links is adding the settings key.
	 *
	 * @covers Kindred\USGS\Admin\Admin::add_action_links
	 */
	public function test_add_action_links() {
		$this->assertArrayHasKey( 'settings', $this->admin->add_action_links( [] ) );
	}
}
