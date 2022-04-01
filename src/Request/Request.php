<?php
namespace Kindred\USGS\Request;

class Request {
	/**
	 * Makes USGS Call
	 *
	 * @param string $url The url to make the request to.
	 *
	 * @return mixed|array|WP_Error
	 */
	public function get_usgs( $url ) {
		$args     = [
			'sslverify' => false,
			'timeout'   => 45,
		];
		$response = wp_safe_remote_get( $url, $args );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'response_error', 'Cannot connect to USGS.' );
		}

		return [
			'response_code'    => wp_remote_retrieve_response_code( $response ),
			'response_message' => wp_remote_retrieve_response_message( $response ),
			'body'             => wp_remote_retrieve_body( $response ),
		];
	}
}
