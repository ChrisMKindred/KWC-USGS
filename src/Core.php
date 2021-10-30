<?php
namespace Kindred\USGS;

use Kindred\USGS\Settings\Settings;
use Kindred\USGS\Shortcode\Shortcode;

final class Core {

	const VERSION     = '21.05.01';
	const PLUGIN_NAME = 'usgs-stream-flow-data';

	private static $instance;

	public static function instance(): self {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init( $file ) {
		$settings = new Settings( $file );
		$shortcode = new Shortcode();
		add_action( 'admin_enqueue_scripts', [ $this, 'register_admin_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_public_scripts' ] );

		add_action( 'admin_footer', array( $this, 'kwcusgsajax_javascript' ) );
		add_action( 'wp_ajax_kwcusgsajax', array( $this, 'kwcusgsajax_callback' ) );
		add_shortcode( 'USGS', array( $shortcode, 'USGS' ) );
	}

	private function __construct() {
		define( 'USGS_PATH', trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ) );
		define( 'USGS_URL', plugin_dir_url( USGS_PATH . self::PLUGIN_NAME ) );
		define( 'USGS_VERSION', self::VERSION );
	}

	private function __clone() {
	}

	public static function activate() {
		return;
	}

	public static function deactivate() {
		return;
	}

	public function register_admin_scripts() {
		$screen = get_current_screen();
		if ( 'settings_page_kwcusgs' === $screen->id ) {
			wp_enqueue_style( self::PLUGIN_NAME . '-admin-styles', USGS_URL . '/admin/assets/css/admin.css', [], self::VERSION );
			wp_enqueue_script( self::PLUGIN_NAME . '-admin-script', USGS_URL . '/admin/assets/js/admin.js', [ 'jquery' ], self::VERSION );
		}
	}

	public function register_public_scripts() {
		wp_enqueue_style( self::PLUGIN_NAME . '-plugin-styles', USGS_URL . '/public/assets/css/public.css', [], self::VERSION );
		wp_enqueue_script( self::PLUGIN_NAME . '-plugin-script', USGS_URL . '/public/assets/js/public.js', [ 'jquery' ], self::VERSION );
	}

	/**
	 * The kwcusgsajax_javascript function
	 *
	 * @return void
	 */
	public function kwcusgsajax_javascript() {
		?>
		<script type="text/javascript" >
		$j=jQuery.noConflict();
		$j(document).ready(function() {
			$j('.button-secondary').click(function() {
				$j( "#results" ).html("Loading Stations...")
				var data = {
					action: 'kwcusgsajax',
					state: $j( ".state" ).val()
				};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$j.post(ajaxurl, data, function(response) {
					$j( "#results" ).html( response );
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * The kwcusgsajax_callback function
	 *
	 * @return void
	 */
	public function kwcusgsajax_callback() {
		$state = $_POST['state'];
		$url   = "https://waterservices.usgs.gov/nwis/iv?stateCd=$state&format=waterml&parameterCd=00060";
		$args     = array(
			'sslverify' => false,
			'timeout'   => 45,
		);
		$data  = wp_safe_remote_get( $url, $args );
		if ( is_wp_error( $data ) ) {
			error_log( 'Error retrieving: ' . esc_url( $url ) );
			error_log( 'Error message:' . $data->get_error_message() );
			exit;
		}
		$data = $data['body'];
		$data = str_replace( 'ns1:', '', $data );
		// Load the XML returned into an object for easy parsing.
		$xml_tree = simplexml_load_string( $data );

		if ( false === $xml_tree ) {
			error_log( 'Unable to parse USGS\'s XML' );
			exit;
		}

		$page = "<table class='widefat'>
					<thead>
					    <tr>
					        <th>Site Code</th>
					        <th>Site Name</th>
					        <th>Latitude / Longitude</th>
					    </tr>
					</thead>
					<tfoot>
						<tr>
					        <th>Site Code</th>
					        <th>Site Name</th>
					        <th>Latitude / Longitude</th>
						</tr>
					</tfoot>";
		$cnt  = 0;
		// phpcs:disable
		/**
		 * phpcs is disabled because the data coming from the api is not formed
		 * correctly to match the valid snake_case format required by the CS.
		 */
		foreach ( $xml_tree->timeSeries as $site_data ) {
			$cnt = ++$cnt;
			$site = $site_data->sourceInfo->siteCode;
			$name = ucwords( strtolower( $site_data->sourceInfo->siteName ) );
			$lat = $site_data->sourceInfo->geoLocation->geogLocation->latitude;
			$long = $site_data->sourceInfo->geoLocation->geogLocation->longitude;
				$page .= "<tbody>
						    <tr>
						      	<td>". $site_data->sourceInfo->siteCode ."</td>
						      	<td><a href='//waterdata.usgs.gov/nwis/uv?" . $site . "' target='_blank'>". ucwords( strtolower( $site_data->sourceInfo->siteName ) ) ."</a></td>
						      	<td><a href='//maps.google.com/?q=" . $lat . "," . $long ."' target='_blank'>" . $lat . " / " . $long . "</a></td>
						    </tr>";
		}
			$page .= '</tbody></table>';
		// phpcs:enable
		echo $page;
		die();
	}
}
