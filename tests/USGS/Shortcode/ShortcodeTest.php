<?php
/**
 * Class SampleTest
 *
 * @package Plugin_Test
 */

use Kindred\USGS\Request\Request;
use Kindred\USGS\Shortcode\Shortcode;

/**
 * Sample test case.
 */
class ShortcodeTest extends WP_UnitTestCase {
	protected $shortcode;
	protected $testPostId;

	public function setUp(): void {
		parent::setUp();
		$request          = new Request();
		$this->shortcode  = new Shortcode( $request );
	}

	public function tearDown(): void {
		wp_delete_post( $this->testPostId, true );
	}

	/**
	 * Tests to make sure that the add action links is adding the settings key.
	 *
	 * @covers Kindred\USGS\Shortcode\Shortcode::__construct
	 */
	public function test_construct() {
		$this->assertInstanceOf( 'Kindred\USGS\Shortcode\Shortcode', $this->shortcode );
	}

	/**
	 * Tests the usgs function in the shortcode.
	 *
	 * @covers Kindred\USGS\Shortcode\Shortcode::USGS
	 * @covers views\usgs.php
	 */
	public function test_usgs() {
		$location = '0209387778';

		$content = do_shortcode( "[USGS location='{$location}' /]" );
		$this->assertContains( "<div class='KWC_USGS clearfix'>", $content );
		$this->assertNotContains( "img", $content );

		$content = do_shortcode( "[USGS location='{$location}' /]" );
		$this->assertContains( "<div class='KWC_USGS clearfix'>", $content );

		$content = do_shortcode( "[USGS location='{$location}' graph='show' /]" );
		$this->assertContains( "<img", $content );

		$title   = 'test title';
		$content = do_shortcode( "[USGS location='{$location}' graph='show' title='{$title}' /]" );
		$this->assertContains( $title, $content );

		// Location 1 doesn't exist so this should return an empty string.
		$content = do_shortcode( "[USGS location='1' /]" );
		$this->assertContains( '', $content );
	}
}
