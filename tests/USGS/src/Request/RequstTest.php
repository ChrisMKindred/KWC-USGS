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

	/**
	 * Does the options page actually exists for the admin user.
	 *
	 * @covers Kindred\USGS\Request\Request::get_usgs
	 */
	public function test_good_request() {
		$url    = 'https://waterservices.usgs.gov/nwis/iv?site=07164500&parameterCd=00060&format=waterml';
		$return = $this->request->get_usgs( $url );
		$this->assertArrayHasKey( 'response_code', $return );
		$this->assertEquals( 200, $return['response_code'] );
		$this->assertArrayHasKey( 'response_message', $return );
		$this->assertEquals( 'OK', $return['response_message'] );
		$this->assertArrayHasKey( 'body', $return );
	}
}
