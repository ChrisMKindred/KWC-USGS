<?php
/**
 * Class AdminTests
 *
 * @package Plugin_Test
 */

/**
 * Sample test case.
 */
class AdminTests extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function tearDown() {
		parent::tearDown();
	}
	/**
	 * Undocumented function
	 *
	 * @covers Kwc_Usgs_Admin::get_instance
	 * @covers Kwc_Usgs_Admin::enqueue_admin_styles
	 */
	public function test_for_admin() {
		$this->admin = kwc_Usgs_Admin::get_instance();
		$this->assertGreaterThan(0, has_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_admin_scripts' ) ) );
	}
}
