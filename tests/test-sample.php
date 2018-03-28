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

	/**
	 * Tests for plugin slug.
	 */
	function test_plugin_slug() {
		// Replace this with some actual testing code.
		$kwcusgs = Kwc_Usgs::get_instance();
		$plugin_slug = $kwcusgs->get_plugin_slug();
		$this->assertEquals( 'kwcusgs', $plugin_slug );
	}
}
