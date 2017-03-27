<?php
/**
 * @package   USGS Steam Flow Data
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      //www.kindredwebconsulting.com
 * @copyright 2015 Kindred Web Consulting
 */

class kwc_usgs_admin {

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

		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$plugin = kwc_usgs::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

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

		/* if( ! is_super_admin() ) {
			return;
		} */

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
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), kwc_usgs::VERSION );
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
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), kwc_usgs::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: //codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'USGS Stream Flow Data', $this->plugin_slug ),
			__( 'Stream Flow Data', $this->plugin_slug ),
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
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    //codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  //codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
//	public function action_method_name() {
		// @TODO: Define your action hook callback here
//	}

	
	
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

	public function kwcusgsajax_callback() {
		$state = $_POST['state'];

		$url = "https://waterservices.usgs.gov/nwis/iv?stateCd=$state&format=waterml&parameterCd=00060";
		$data = file_get_contents( $url );
		if ( ! $data ){
	 		echo 'Error retrieving: ' . $url;
	 		exit;
		}
	 	$data = str_replace( 'ns1:', '', $data );
	  	// Load the XML returned into an object for easy parsing
	  	$xml_tree = simplexml_load_string( $data );
	  	if ( false === $xml_tree ){
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
		$cnt = 0;
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
			$page .= "</tbody></table>";
			echo $page;
		die();
	}	
}
