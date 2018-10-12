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

	public $instance;

	public function setUp() {
		parent::setUp();
		$this->instance = kwc_Usgs_Admin::get_instance();
	}

	public function tearDown() {
		parent::tearDown();
	}

	/**
	 * Test Constructor of the class and make sure that everything is there.
	 *
	 * @covers Kwc_Usgs_Admin::get_instance
	 * @covers Kwc_Usgs_Admin::__construct
	 */
	public function test_admin_construct() {
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->instance, 'enqueue_admin_styles' ) ) );
		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', array( $this->instance, 'enqueue_admin_scripts' ) ) );
		$this->assertEquals( 10, has_action( 'admin_menu', array( $this->instance, 'add_plugin_admin_menu' ) ) );
		$this->assertEquals( 10, has_action( 'admin_footer', array( $this->instance, 'kwcusgsajax_javascript' ) ) );
		$this->assertEquals( 10, has_action( 'wp_ajax_kwcusgsajax', array( $this->instance, 'kwcusgsajax_callback' ) ) );
	}

}
