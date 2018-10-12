<?php
/**
 * Admin
 *
 * @package    USGS Steam Flow Data
 * @author     Chris Kindred <Chris@kindredwebconsulting.com>
 * @license    GPL-2.0+
 * @link       //www.kindredwebconsulting.com
 * @copyright  2015 Kindred Web Consulting
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    USGS Steam Flow Data
 * @subpackage usgs_stream_flow_data/admin
 */
class Kwc_Usgs_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$this->plugin_slug = Kwc_Usgs::get_instance()->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );
		add_action( 'admin_footer', array( $this, 'kwcusgsajax_javascript' ) );
		add_action( 'wp_ajax_kwcusgsajax', array( $this, 'kwcusgsajax_callback' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), kwc_usgs::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Kwc_Usgs::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'USGS Stream Flow Data', 'kwc_usgs' ),
			__( 'Stream Flow Data', 'kwc_usgs' ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 * @param array $links      The links array.
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'kwc_usgs' ) . '</a>',
			),
			$links
		);

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
		$data  = wp_remote_get( $url );
		if ( ! $data ) {
			echo 'Error retrieving: ' . esc_url( $url );
			exit;
		}
		$data = $data['body'];
		$data = str_replace( 'ns1:', '', $data );
		// Load the XML returned into an object for easy parsing.
		$xml_tree = simplexml_load_string( $data );

		if ( false === $xml_tree ) {
			echo 'Unable to parse USGS\'s XML';
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
