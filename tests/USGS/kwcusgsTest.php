<?php

use Kindred\USGS\Admin\Admin;
use Kindred\USGS\Core;

/**
 * Class KwcusgsTest
 */

class KwcusgsTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function test_usgs_core() {
		$this->assertInstanceOf( 'Kindred\USGS\Core', usgs_core() );
	}

}
