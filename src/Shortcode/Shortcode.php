<?php
namespace Kindred\USGS\Shortcode;

class Shortcode {
	/**
	 * Undocumented function
	 *
	 * @param array  $atts      the attributes passed to the shortcode.
	 * @param string $content   the content.
	 * @return string           the html for the shortcode.
	 */
	public function USGS( $atts, $content = null ) { //phpcs:ignore
		$defaults = array(
			'location' => '09080400',
			'title'    => null,
			'graph'    => null,
		);
		$atts     = shortcode_atts( $defaults, $atts );
		$location = $atts['location'];
		$title    = $atts['title'];
		$graph    = $atts['graph'];
		if ( ! $response = get_transient( 'kwc_usgs-' . $location ) ) {
			$response = $this->get_usgs( $location );
			if ( is_wp_error( $response ) ) {
				return $response->get_error_message();
			}

			if ( ! $response['response_code'] ) {
				return $response['response_message'];
			}

			set_transient( 'kwc_usgs-' . $location, $response, HOUR_IN_SECONDS * 12 );
		}

		$data = str_replace( 'ns1:', '', $response['data'] );

		$xml_tree = simplexml_load_string( $data );
		if ( false === $xml_tree ) {
			return 'Unable to parse USGS\'s XML';
		}

		if ( ! isset( $title ) ) {
			$site_name = $xml_tree->timeSeries->sourceInfo->siteName; //phpcs:ignore
		} else {
			if ( '' == $title ) {
				$site_name = $xml_tree->timeSeries->sourceInfo->siteName; //phpcs:ignore
			} else {
				$site_name = $title;
			}
		}

		$the_page  = "<div class='KWC_USGS clearfix'>
						<h3 class='header'>$site_name</h3>
							<ul class='sitevalues'>";
		$graphflow = '';
		$graphgage = '';
		foreach ( $xml_tree->timeSeries as $site_data ) { //phpcs:ignore
			if ( '' == $site_data->values->value ) {
				$value = '-';
			} elseif ( -999999 == $site_data->values->value ) {
					$value       = 'UNKNOWN';
					$provisional = '-';
			} else {
				$desc = $site_data->variable->variableName;
				switch ( $site_data->variable->variableCode ) {
					case '00010':
						$value         = $site_data->values->value;
						$degf          = ( 9 / 5 ) * (float) $value + 32;
						$watertemp     = $degf;
						$watertempdesc = '&deg; F';
						$the_page     .= "<li class='watertemp'>Water Temp: $watertemp $watertempdesc</li>";
						break;

					case '00060':
						$split_desc     = explode( ',', $desc );
						$value          = $site_data->values->value;
						$streamflow     = $value;
						$streamflowdesc = $split_desc[1];
						$the_page      .= "<li class='flow'>Flow: $streamflow $streamflowdesc</li>";
						$graphflow      = "<img src='https://waterdata.usgs.gov/nwisweb/graph?agency_cd=USGS&site_no=$location&parm_cd=00060&rand=" . rand() . "'/>";
						break;

					case '00065':
						$split_desc     = explode( ',', $desc );
						$value          = $site_data->values->value;
						$gageheight     = $value;
						$gageheightdesc = $split_desc[1];
						$the_page      .= "<li class='gageheight'>Water Level: $gageheight $gageheightdesc</li>";
						$graphgage      = "<img src='https://waterdata.usgs.gov/nwisweb/graph?agency_cd=USGS&site_no=$location&parm_cd=00065&rand=" . rand() . "'/>";
						break;
				}
			}
		}
		$the_page .= '</ul>';
		if ( isset( $graph ) ) {
			if ( 'show' == $graph ) {
				$the_page .= "<div class='clearfix'>$graphgage . $graphflow</div>";
			}
		}
		$the_page .= "<a class='clearfix' href='https://waterdata.usgs.gov/nwis/uv?$location' target='_blank'>USGS</a>";
		$the_page .= '</div>';

		return $the_page;
	}

		/**
	 * Makes USGS Call
	 *
	 * @param string $location  the location to make call with.
	 * @return mixed|array|WP_Error
	 */
	public function get_usgs( $location ) {
		$url      = "https://waterservices.usgs.gov/nwis/iv?site=$location&parameterCd=00010,00060,00065&format=waterml";
		$args     = array(
			'sslverify' => false,
			'timeout'   => 45,
		);
		$response = wp_safe_remote_get( $url, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		return array(
			'response_code'    => wp_remote_retrieve_response_code( $response ),
			'response_message' => wp_remote_retrieve_response_message( $response ),
			'data'             => wp_remote_retrieve_body( $response ),
		);
	}
}
