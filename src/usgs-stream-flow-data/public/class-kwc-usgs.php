<?php
/**
 * Public
 *
 * @package    USGS Steam Flow Data
 * @author     Chris Kindred <Chris@kindredwebconsulting.com>
 * @license    GPL-2.0+
 * @link       //www.kindredwebconsulting.com
 */

/**
 * The main class for the plugin
 *
 * @package    USGS Steam Flow Data
 * @subpackage public
 * @author     Chris Kindred <Chris@kindredwebconsulting.com>
 * @license    GPL-2.0+
 * @link       //www.kindredwebconsulting.com
 */
class Kwc_Usgs {
	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '20.08.01';

	/**
	 * Unique identifier for your plugin.
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
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'USGS', array( $this, 'USGS' ) );
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
	 * @since 1.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
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
			if ( $network_wide ) {
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
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 2.7
	 */
	private static function single_activate() {
		return;
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 2.7
	 * @return void
	 */
	private static function single_deactivate() {

	}
	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 * @param boolean $network_wide True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
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
	 * @since 1.0.0
	 * @param int $blog_id ID of the new blog.
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
	 * @since 1.0.0
	 * @return array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {
		global $wpdb;
		return $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE archived = %s AND spam = %s AND deleted = %s", array( '0', '0', '0' ) ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'kwc_usgs_plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    //codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  //codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */

	/**
	 * This needs to be split into different functions
	 *
	 * @since 1.0.0
	 */
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
		$the_page = get_transient( 'kwc_usgs-' . $location . $graph . $title );

		if ( ! $the_page ) {
			$response = $this->get_usgs( $location );

			if ( is_wp_error( $response ) ) {
				return $response->get_error_message();
			}

			if ( ! $response['response_code'] ) {
				return $response['response_message'];
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

			set_transient( 'kwc_usgs-' . $location . $graph . $title, $the_page, 60 * 15 );
		}
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
