<?php

/**
 * Class KwcusgsTest
 */
class KwcusgsTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	/**
	 * Tests the core instance
	 *
	 * @covers ::usgs_core
	 */
	public function test_usgs_core() {
		$this->assertInstanceOf( 'Kindred\USGS\Core', usgs_core() );
	}

}
