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

	/**
	 * Test the enqueue_admin_styles method
	 *
	 * @covers Kwc_Usgs_Admin::enqueue_admin_styles
	 */
	public function test_admin_enqueue_admin_styles() {
		set_current_screen( 'front' );
		$this->instance->enqueue_admin_styles();
		$this->assertFalse( wp_style_is( 'kwcusgs-admin-styles' ) );

		set_current_screen( 'settings_page_kwcusgs' );
		$this->instance->enqueue_admin_styles();
		$this->assertTrue( wp_style_is( 'kwcusgs-admin-styles' ) );
	}

	/**
	 * Test the enqueue_admin_scripts method
	 *
	 * @covers Kwc_Usgs_Admin::enqueue_admin_scripts
	 */
	public function test_admin_enqueue_admin_scripts() {
		set_current_screen( 'front' );
		$this->instance->enqueue_admin_scripts();
		$this->assertFalse( wp_script_is( 'kwcusgs-admin-script' ) );

		set_current_screen( 'settings_page_kwcusgs' );
		$this->instance->enqueue_admin_scripts();
		$this->assertTrue( wp_script_is( 'kwcusgs-admin-script' ) );
	}
}
