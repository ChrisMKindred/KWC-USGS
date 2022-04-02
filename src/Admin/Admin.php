<?php
namespace Kindred\USGS\Admin;

use Kindred\USGS\Core;
use Kindred\USGS\Request\Request;

class Admin {
	/**
	 * @var Request
	 */
	protected $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * Adds the plugin menu to the admin panel.
	 *
	 * @return void
	 */
	public function add_plugin_admin_menu() {
		add_options_page(
			__( 'USGS Stream Flow Data', 'kwc_usgs' ),
			__( 'Stream Flow Data', 'kwc_usgs' ),
			'manage_options',
			Core::PLUGIN_NAME,
			[ $this, 'display_plugin_admin_page' ]
		);
	}

	/**
	 * Adds the action links in the plugin listing.
	 *
	 * @param array<int, string> $links
	 *
	 * @return array<int|string, string>
	 */
	public function add_action_links( $links ) {
		$setting_link = [
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . Core::PLUGIN_NAME ) . '">' . __( 'Settings', 'kwc_usgs' ) . '</a>',
		];
		return array_merge( $setting_link, $links );
	}

	/**
	 * The kwcusgsajax_callback function
	 *
	 * @return void
	 */
	public function kwcusgsajax_callback() {
		$state = $_POST['state'];
		$url   = "https://waterservices.usgs.gov/nwis/iv?stateCd=$state&format=waterml&parameterCd=00060";

		if ( ! $response = get_transient( 'kwc_usgs_admin-' . md5( $url ) ) ) {
			$response = $this->request->get_usgs( $url );

			if ( is_wp_error( $response ) ) {
				echo $response->get_error_message();
				die();
			}

			if ( ! $response['response_code'] ) {
				echo $response['response_message'];
				die();
			}

			set_transient( 'kwc_usgs_admin-' . md5( $url ), $response, HOUR_IN_SECONDS * 24 );
		}


		$data = str_replace( 'ns1:', '', $response['body'] );
		// Load the XML returned into an object for easy parsing.
		$xml_tree = simplexml_load_string( $data );

		if ( false === $xml_tree ) {
			_e( 'Unable to parse USGS\'s XML', 'kwc_usgs' );
			die();
		}

		$page = "<table class='widefat'>
					<thead>
					    <tr>
					        <th>" . __( 'Site Code', 'kwc_usgs' ) . "</th>
					        <th>" . __( 'Site Name', 'kwc_usgs' ) . "</th>
					        <th>" . __( 'Latitude / Longitude', 'kwc_usgs' ) . "</th>
					    </tr>
					</thead>
					<tfoot>
						<tr>
							<th>" . __( 'Site Code', 'kwc_usgs' ) . "</th>
							<th>" . __( 'Site Name', 'kwc_usgs' ) . "</th>
							<th>" . __( 'Latitude / Longitude', 'kwc_usgs' ) . "</th>
						</tr>
					</tfoot>";
		$cnt  = 0;

		foreach ( $xml_tree->timeSeries as $site_data ) {
			$cnt   = ++$cnt;
			$site  = $site_data->sourceInfo->siteCode;
			$lat   = $site_data->sourceInfo->geoLocation->geogLocation->latitude;
			$long  = $site_data->sourceInfo->geoLocation->geogLocation->longitude;
			$page .= "<tbody>
						<tr>
							<td>" . $site_data->sourceInfo->siteCode ."</td>
							<td><a href='//waterdata.usgs.gov/nwis/uv?" . $site . "' target='_blank'>". ucwords( strtolower( $site_data->sourceInfo->siteName ) ) ."</a></td>
							<td><a href='//maps.google.com/?q=" . $lat . "," . $long ."' target='_blank'>" . $lat . " / " . $long . "</a></td>
						</tr>";
		}
		$page .= '</tbody></table>';
		echo $page;
		die();
	}

	/**
	 * Displayes the admin page.
	 *
	 * @return void
	 */
	public function display_plugin_admin_page() {
		include_once( USGS_PATH . '/views/admin.php' );
	}
}
