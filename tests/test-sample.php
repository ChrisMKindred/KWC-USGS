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
		$plugin_slug = $this->plugin->get_plugin_slug();
		$this->assertEquals( 'kwcusgs', $plugin_slug );
	}

	/**
	 * Tests for plugin actions.
	 * @covers Kwc_Usgs::__construct
	 */
	public function test_public_actions() {
		$this->assertEquals( 10, has_action( 'init', array( $this->plugin, 'load_plugin_textdomain' ) ) );
		$this->assertEquals( 10, has_action( 'wpmu_new_blog', array( $this->plugin, 'activate_new_site' ) ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $this->plugin, 'enqueue_styles' ) ) );
		$this->assertEquals( 10, has_action( 'wp_enqueue_scripts', array( $this->plugin, 'enqueue_scripts' ) ) );
		$this->assertTrue( shortcode_exists( 'USGS' ) );
	}

	public function provider_get_usgs() {
		return array(
				array( '09080400', 200 ),
				array( '', '400' ),
			);
	}
	/**
	 * Test USGS remote call
	 * @dataProvider provider_get_usgs
	 * @covers Kwc_Usgs::get_usgs
	 *
	 * FIXME:
	 * This isn't a very good test. It is basically checking to see if there is
	 * a response from the USGS server but that is outside of our scope and can
	 * cause failures when the error is on their side.  Not sure of a better
	 * solution yet.
	 *
	 */
	public function test_get_usgs_call( $location, $response_code ) {
		$response = $this->plugin->get_usgs( $location );
		if ( ! is_wp_error( $response ) ) {
			$this->assertEquals( $response_code, $response['response_code'] );
		} else {
			$this->markTestSkipped(
				'Error returned from USGS Call: ' . print_r( $response->get_error_messages(), true )
			);
		}
	}
}
