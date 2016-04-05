<?php
/**
 *
 *
 * @package   USGS Steam Flow Data
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      http://www.kindredwebconsulting.com
 * @copyright 2015 Kindred Web Consulting
 */

class kwc_usgs {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '2.4.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'kwcusgs';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_shortcode( "USGS", array( $this, 'USGS' ) );


		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		//  add_action( '@TODO', array( $this, 'action_method_name' ) );
		//  add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param int     $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {

	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );

	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */

	/**
	 * This needs to be split into different functions
	 *
	 * @since 	1.0.0
	 */

	public function USGS( $atts, $content = null ) {
		extract( shortcode_atts(
				array(
					'location'  => '09080400',
					'title'  => null,
					'graph'  => null
				), $atts ) );

		$thePage = get_transient( 'kwc_usgs-' . $location . $graph . $title );

		if ( !$thePage ) {
			$url = "http://waterservices.usgs.gov/nwis/iv?site=$location&parameterCd=00010,00060,00065&format=waterml";

			$response = wp_remote_get( $url );
			$data = wp_remote_retrieve_body( $response );

			if ( ! $data ) {
				return 'USGS Not Responding.';
			}

			$data = str_replace( 'ns1:', '', $data );

			$xml_tree = simplexml_load_string( $data );
			if ( False === $xml_tree ) {
				return 'Unable to parse USGS\'s XML';
			}

			if ( ! isset( $title )  ) {
				$SiteName = $xml_tree->timeSeries->sourceInfo->siteName;
			} else {
				if ( $title == '' ) {
					$SiteName = $xml_tree->timeSeries->sourceInfo->siteName;
				} else {
					$SiteName = $title;
				}
			}

			$thePage = "<div class='KWC_USGS clearfix'>
							<h3 class='header'>$SiteName</h3>
								<ul class='sitevalues'>";
			$graphflow = "";
			$graphgage = "";
			foreach ( $xml_tree->timeSeries as $site_data ) {
				if ( $site_data->values->value == '' ) {
					$value = '-';
				} else if ( $site_data->values->value == -999999 ) {
						$value = 'UNKNOWN';
						$provisional = '-';
				} else {
					$desc = $site_data->variable->variableName;
					switch ( $site_data->variable->variableCode ) {
					case "00010":
						$value  = $site_data->values->value;
						$degf   = ( 9 / 5 ) * (float)$value + 32;
						$watertemp      = $degf;
						$watertempdesc  = "&deg; F";
						$thePage .= "<li class='watertemp'>Water Temp: $watertemp $watertempdesc</li>";
						break;

					case "00060":
						$splitDesc = explode( ",", $desc );
						$value  = $site_data->values->value;
						$streamflow     = $value;
						$streamflowdesc = $splitDesc[1];
						$thePage .= "<li class='flow'>Flow: $streamflow $streamflowdesc</li>";
						$graphflow = "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00060" . "&" . rand() . "'/>";
						break;

					case "00065":
						$splitDesc = explode( ",", $desc );
						$value  = $site_data->values->value;
						$gageheight = $value;
						$gageheightdesc = $splitDesc[1];
						$thePage .= "<li class='gageheight'>Water Level: $gageheight $gageheightdesc</li>";
						$graphgage = "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00065" . "&" . rand() . "'/>";
						break;
					}
				}
			}
			$thePage .=  "</ul>";
			if ( isset( $graph ) ) {
				if ( $graph == 'show' ) {
					$thePage .= "<div class='clearfix'>";
					$thePage .= $graphgage . $graphflow;
					$thePage .= "</div>";
				}
			}
			$thePage .= "<a class='clearfix' href='http://waterdata.usgs.gov/nwis/uv?$location' target='_blank'>USGS</a>";
			$thePage .= "</div>";

			set_transient( 'kwc_usgs-' . $location . $graph . $title, $thePage, 60 * 15 );
		}
		return $thePage;
	}

}
