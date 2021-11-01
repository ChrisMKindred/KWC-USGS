<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

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
}
