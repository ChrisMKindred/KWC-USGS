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
class AdminTest extends WP_UnitTestCase {
	protected $admin;

	public function setUp(){
		parent::setUp();
		$request = new Request();
		$this->admin = new Admin( $request );
	}

	/**
	 * Does the options page actually exists for the admin user.
	 *
	 * @covers Kindred\USGS\Admin\Admin::add_plugin_admin_menu
	 */
	public function test_admin_add_plugin_admin_menu() {
		wp_set_current_user(
			self::factory()->user->create(
				array(
            		'role' => 'administrator',
				)
			)
		);
		$this->admin->add_plugin_admin_menu();
		$this->assertNotEmpty( menu_page_url( Core::PLUGIN_NAME, false ) );
	}
}
