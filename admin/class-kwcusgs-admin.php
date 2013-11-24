<?php
/**
 * USGS Steam Flow.
 *
 * @package   USGS Steam Flow
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      http://www.kindredwebconsulting.com
 * @copyright 2013 Kindred Web Consulting
 */

class kwc_usgs_admin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.0.1
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


		add_action( 'admin_footer', array( $this, 'my_action_javascript' ) );
		add_action( 'wp_ajax_my_action', array( $this, 'my_action_callback') );

		
		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
//		add_action( '@TODO', array( $this, 'action_method_name' ) );
//		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.0.1
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
	 * @since     0.0.1
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
	 * @since     0.0.1
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
	 * @since    0.0.1
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'USGS Stream Flow Data', $this->plugin_slug ),
			__( 'USGS', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    0.0.1
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
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.0.1
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

		
	public function my_action_javascript() {
?>
		<script type="text/javascript" >
		$j=jQuery.noConflict();
		$j(document).ready(function() {
			$j('.button-secondary').click(function() {
				$j( "#results" ).html("Loading Stations...")
				var data = {
					action: 'my_action',
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

	public function my_action_callback() {
		$state = $_POST['state'];

		$url = "http://waterservices.usgs.gov/nwis/iv?stateCd=$state&format=waterml&parameterCd=00060";
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
					        <th></th>
					        <th>Site Name</th>
					        <th>Site Code</th>       
					        <th>Latitude</th>
					        <th>Longitude<th>
					    </tr>
					</thead>
					<tfoot>
						<tr>
					        <th></th>
					        <th>Site Name</th>
					        <th>Site Code</th>       
					        <th>Latitude</th>
					        <th>Longitude<th>
						</tr>
					</tfoot>";
		foreach ( $xml_tree->timeSeries as $site_data ) {	
			$cnt = ++$cnt;
				$page .= "<tbody>
						    <tr>
						      	<td>". $cnt ."</td>
						      	<td>". ucwords( strtolower( $site_data->sourceInfo->siteName ) ) ."</td>
						      	<td>". $site_data->sourceInfo->siteCode ."</td>
						      	<td>". $site_data->sourceInfo->geoLocation->geogLocation->latitude ."</td>
						      	<td>". $site_data->sourceInfo->geoLocation->geogLocation->longitude ."</td>
						    </tr>";
		}
			$page .= "</tbody></table>";
			echo $page;
		die();
	}	




	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.0.1
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
