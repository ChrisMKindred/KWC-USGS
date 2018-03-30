<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {
	protected $plugin;

	public function setUp(){
		parent::setUp();
		$this->plugin = Kwc_Usgs::get_instance();
	}
	/**
	 * Tests for plugin slug.
	 * @covers Kwc_Usgs::get_instance
	 * @covers Kwc_Usgs::get_plugin_slug
	 */
	public function test_plugin_slug() {
		// Replace this with some actual testing code.
		$plugin_slug = $this->plugin->get_plugin_slug();
		$this->assertEquals( 'kwcusgs', $plugin_slug );
	}


	public function provider_get_usgs() {
		return array(
				array( '09080400', 200 ),
			);
	}
	/**
	 * Test USGS remote call
	 * @dataProvider provider_get_usgs
	 * @covers Kwc_Usgs::get_usgs
	 */
	public function test_get_usgs_call( $location, $response_code ) {
		$response = $this->plugin->get_usgs( $location );
		$this->assertEquals( $response_code, $response['response_code'] );
	}
}
