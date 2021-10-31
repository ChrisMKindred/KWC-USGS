<?php
namespace Kindred\USGS\Shortcode;

use Kindred\USGS\Request\Request;

class Shortcode {

	protected $location = '';
	protected $title = '';
	protected $graph = '';
	protected $gageheight = [];
	protected $flow = [];
	protected $watertemp = [];
	private $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Undocumented function
	 *
	 * @param array  $atts      the attributes passed to the shortcode.
	 * @param string $content   the content.
	 * @return string           the html for the shortcode.
	 */
	public function USGS( $atts, $content = null ) {
		$defaults = array(
			'location' => '09080400',
			'title'    => null,
			'graph'    => null,
		);
		$atts     = shortcode_atts( $defaults, $atts );

		$this->location = $atts['location'];
		$this->title    = $atts['title'];
		$this->graph    = $atts['graph'];

		if ( ! $response = get_transient( 'kwc_usgs-' . md5( $this->location ) ) ) {
			$url = 'https://waterservices.usgs.gov/nwis/iv?site=' . $this->location . '&parameterCd=00010,00060,00065&format=waterml';
			$response = $this->request->get_usgs( $url );
			if ( is_wp_error( $response ) ) {
				return $response->get_error_message();
			}

			if ( ! $response['response_code'] ) {
				return $response['response_message'];
			}

			set_transient( 'kwc_usgs-' . md5( $this->location ), $response, MINUTE_IN_SECONDS * 60 );
		}

		$data = str_replace( 'ns1:', '', $response['body'] );

		$xml_tree = simplexml_load_string( $data );
		if ( false === $xml_tree ) {
			return __( 'Unable to parse USGS\'s XML', 'kwc-usgs' );
		}

		if ( ! $this->title || 0 === strlen( $this->title ) ) {
			$this->title = $xml_tree->timeSeries->sourceInfo->siteName;
		}

		foreach ( $xml_tree->timeSeries as $site_data ) { //phpcs:ignore
			if ( '' !== $site_data->values->value && -999999 !== $site_data->values->value ) {
				$desc = $site_data->variable->variableName;
				switch ( $site_data->variable->variableCode ) {
					case '00010':
						$split_desc     = explode( ',', $desc );
						$value         = $site_data->values->value;
						$degf          = ( 9 / 5 ) * (float) $value + 32;
						$this->watertemp = [
							'class'       => 'watertemp',
							'name'        => $split_desc[0],
							'value'       => $degf,
							'description' => '&deg; F',
							'graph'       => 'https://waterdata.usgs.gov/nwisweb/graph?agency_cd=USGS&site_no=' . $this->location . '&parm_cd=00010&rand=' . rand(),
						];
						break;

					case '00060':
						$split_desc     = explode( ',', $desc );
						$this->flow = [
							'class'       => 'flow',
							'name'        => $split_desc[0],
							'value'       => $site_data->values->value,
							'description' => $split_desc[1],
							'graph'       => 'https://waterdata.usgs.gov/nwisweb/graph?agency_cd=USGS&site_no=' . $this->location . '&parm_cd=00060&rand=' . rand(),
						];
						break;

					case '00065':
						$split_desc     = explode( ',', $desc );
						$this->gageheight = [
							'class'       => 'gageheight',
							'name'        => $split_desc[0],
							'value'       => $site_data->values->value,
							'description' => $split_desc[1],
							'graph'       => 'https://waterdata.usgs.gov/nwisweb/graph?agency_cd=USGS&site_no=' . $this->location . '&parm_cd=00065&rand=' . rand(),
						];
						break;
				}
			}
		}

		$templates = [
			sprintf( 'usgs-%s.php', $this->location ),
			'usgs.php',
		];

		$site_args = [
			'location'   => $this->location,
			'title'      => $this->title,
			'graph'      => $this->graph,
			'gageheight' => $this->gageheight,
			'flow'       => $this->flow,
			'watertemp'  => $this->watertemp,
		];

		ob_start();
		$template = locate_template( $templates );
		if ( empty( $template ) ) {
			$template = USGS_PATH . '/views/usgs.php';
		}
		load_template( $template, true, $site_args );
		return ob_get_clean();
	}
}
