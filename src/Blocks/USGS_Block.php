<?php

namespace Kindred\USGS\Blocks;

class USGS_Block {

	public function init() {
		register_block_type(
			USGS_PATH . 'build/usgs/block.json',
			[ 'render_callback' => [ $this, 'render_callback' ] ]
		);
	}

	public function render_callback( $attributes, $content ) {
		return do_shortcode( "[USGS location='{$attributes['message']}' /]" );
	}
}
