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
		$plugin_slug = kwc_usgs::get_plguin_slog();
		$this->assertEquals( 'kwcusgs', $plugin_slug );
	}
}
