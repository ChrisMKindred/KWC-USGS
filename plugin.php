<?php
/*
Plugin Name: USGS Steam Flow
Plugin URI: http://www.kindredwebconsulting.com
Description: Display USGS Stream Flow Data
Version: 1.0
Author: Chris Kindred - Kindred Web Consulting
Author URI: http://www.kindredwebconsulting.com
Author Email: Support@kindredwebconsulting.com
License:

  Copyright 2013 Kindred Web Consulting (support@kindredwebconsulting.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class KWCUSGS {

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

		// Register admin styles and scripts
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );


		add_action( 'admin_menu', array( $this, 'setmenu' ));
		add_action( 'admin_init', array( $this, 'register_mysettings' ));

	    /*
	     * TODO:
	     * Define the custom functionality for your plugin. The first parameter of the
	     * add_action/add_filter calls are the hooks into which your code should fire.
	     *
	     * The second parameter is the function name located within this class. See the stubs
	     * later in the file.
	     *
	     * For more information:
	     * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
	     */


	    add_shortcode( "USGS", array( $this, 'USGS' ));
	    
	    add_filter( 'widget_text', 'do_shortcode' );

	} // end constructor

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function activate( $network_wide ) {
		// TODO:	Define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// TODO:	Define deactivation functionality here
	} // end deactivate

	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function uninstall( $network_wide ) {
		// TODO:	Define uninstall functionality here
	} // end uninstall

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {
		$domain = 'kwc-usgs-locale';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'kwc-usgs-admin-styles', plugins_url( 'kwcusgs/css/admin.css' ) );

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'kwc-usgs-admin-script', plugins_url( 'kwcusgs/js/admin.js' ), array('jquery') );

	} // end register_admin_scripts


	public function register_mysettings() { // whitelist options
  		register_setting( 'myoption-group', 'new_option_name' );
  		register_setting( 'myoption-group', 'some_other_option' );
  		register_setting( 'myoption-group', 'option_etc' );
	}

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {

		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'kwc-usgs-plugin-styles', plugins_url( 'kwcusgs/css/display.css' ) );

	} // end register_plugin_styles

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {

		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'kwc-usgs-plugin-script', plugins_url( 'kwcusgs/js/display.js' ), array('jquery') );

	} // end register_plugin_scripts

	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

	/**
 	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *		  WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *		  Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 */
	function USGS($atts, $content = null ) {
	   	extract( shortcode_atts( 
	   		array(
	   			'location' 	=> '09080400',
	   			'title'		=> null,
	   			'graph'		=> null
	   			), $atts ) );
	
		$url = "http://waterservices.usgs.gov/nwis/iv?site=$location&parameterCd=00010,00060,00065&format=waterml";
		
		$response = wp_remote_get($url);
	  	$data = wp_remote_retrieve_body( $response );
	  	if (!$data){
	   		return 'USGS Not Responding.';
	  	}
		
		// Remove the namespace prefix for easier parsing
		$data = str_replace('ns1:','', $data);
		
		// Load the XML returned into an object for easy parsing
		$xml_tree = simplexml_load_string($data);
		
		if ($xml_tree === FALSE){
			return 'Unable to parse USGS\'s XML';
		}
		if (!isset($title)){
			$SiteName = $xml_tree->timeSeries->sourceInfo->siteName;
		} else {
			$SiteName = $title;
		}
		$latitude  = (double) $xml_tree->timeSeries->sourceInfo->geoLocation->geogLocation->latitude; //North
		$longitude = (double) $xml_tree->timeSeries->sourceInfo->geoLocation->geogLocation->longitude; //West
		$time_format = 'h:i:s A';
		$thePage = "<div class='KWC_USGS clearfix'>
						<h3 class='header'>$SiteName</h3>
							<ul class='sitevalues'>";
	  	foreach ($xml_tree->timeSeries as $site_data){	
	    	if ($site_data->values->value == '') {
	      		$value = '-';
	    	} else if ($site_data->values->value == -999999){
	      		$value = 'UNKNOWN';
	      		$provisional = '-';
	    	} else {
	      		$desc   = $site_data->variable->variableName;
	      		switch ($site_data->variable->variableCode) {
	        		case "00010":
	          			$value  = $site_data->values->value;
	          			$degf   = (9/5) * $value + 32;          			       
	          			$watertemp      = $degf;
	          			$watertempdesc  = "&deg; F"; 
	          			$thePage .= "<li class='watertemp'>Water Temp: $watertemp $watertempdesc</li>";
	          			break;
			        case "00060":
			          	$splitDesc = explode(",",$desc);
			          	$value  = $site_data->values->value;
			          	$streamflow     = $value;
			          	$streamflowdesc = $splitDesc[1];
	          			$thePage .= "<li class='flow'>Flow: $streamflow $streamflowdesc</li>";
			          	break;
			        case "00065":
			          	$splitDesc = explode(",",$desc);
			          	$value  = $site_data->values->value;
			          	$gageheight = $value;  
			          	$gageheightdesc = $splitDesc[1];          
	          			$thePage .= "<li class='gageheight'>Water Level: $gageheight $gageheightdesc</li>";
			          	break;	
	      		}
	    	}
	  	}
		$thePage .=		"</ul>";
		if (isset($graph)){
			if ($graph == 'true'){
				$thePage .= "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00060'/>";
				$thePage .= "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00065'/>";
			}
		}
		$thePage .= "<a href='http://waterdata.usgs.gov/nwis/uv?$location'>USGS</a>";
		$thePage .=	"</div>";
	   	return $thePage;
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *		  WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *		  Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 */
	function setmenu() {
		add_submenu_page( 'options-general.php', 'Page title', 'USGS', 'manage_options', 'usgs', array( $this, 'my_magic_function' ) );
	}

	function my_magic_function(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
$state_values=array(
                'AL'=>"Alabama",
                'AK'=>"Alaska",
                'AZ'=>"Arizona",
                'AR'=>"Arkansas",
                'CA'=>"California",
                'CO'=>"Colorado",
                'CT'=>"Connecticut",
                'DE'=>"Delaware",
                'DC'=>"District Of Columbia",
                'FL'=>"Florida",
                'GA'=>"Georgia",
                'HI'=>"Hawaii",
                'ID'=>"Idaho",
                'IL'=>"Illinois",
                'IN'=>"Indiana",
                'IA'=>"Iowa",
                'KS'=>"Kansas",
                'KY'=>"Kentucky",
                'LA'=>"Louisiana",
                'ME'=>"Maine",
                'MD'=>"Maryland",
                'MA'=>"Massachusetts",
                'MI'=>"Michigan",
                'MN'=>"Minnesota",
                'MS'=>"Mississippi",
                'MO'=>"Missouri",
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio",
                'OK'=>"Oklahoma",
                'OR'=>"Oregon",
                'PA'=>"Pennsylvania",
                'RI'=>"Rhode Island",
                'SC'=>"South Carolina",
                'SD'=>"South Dakota",
                'TN'=>"Tennessee",
                'TX'=>"Texas",
                'UT'=>"Utah",
                'VT'=>"Vermont",
                'VA'=>"Virginia",
                'WA'=>"Washington",
                'WV'=>"West Virginia",
                'WI'=>"Wisconsin",
                'WY'=>"Wyoming"
    );
	function listUSStates($state_values,$dropdown_name,$key_selected) {
	    $string="<select name=\"".$dropdown_name."\">\n";
    	if (!empty($state_values)) {
        	if ($key_selected=="" || !isset($key_selected)) {
            	$string.="<option value=\"\"></option>\n";
        	}
        	foreach($state_values as $state_short=>$state_full) {
            	if ($key_selected!="" && $key_selected==$state_short) {
                	$additional=" SELECTED";
            	}
            	else {
                	$additional="";
            	}
                $string.="<option value=\"".$state_short."\"".$additional.">".$state_full."</option>\n";
        	}
    	}
    	$string.="</select>\n";
    	return $string;
	}
	?>
		<div class="wrap">
			<?php screen_icon(); ?> <h2>USGS Stream Flow Data</h2>
			<h3>Get River ID</h3>
			<label>Select State</label>
			<?php 
				$select_box_name = "roger";
				$key_selected = "";
				echo listUSStates($state_values,$select_box_name,$key_selected);
//			$url = 'http://waterservices.usgs.gov/nwis/iv?stateCd=CO&format=waterml&parameterCd=00060';
//			$data = file_get_contents($url);
//			if (!$data){
//			 	echo 'Error retrieving: ' . $url;
//			 	exit;
//			}
//			// Remove the namespace prefix for easier parsing
//			$data = str_replace('ns1:','', $data);
//			// Load the XML returned into an object for easy parsing
//			$xml_tree = simplexml_load_string($data);
//			if ($xml_tree === FALSE){
//			 	echo 'Unable to parse USGS\'s XML';
//			  exit;
//			}
//			echo "<pre>";
//			print_r($xml_tree);
//			echo "</pre>";
			?>
	    </div>
	    <?php 	
	}

	function filter_method_name() {
	    // TODO:	Define your filter method here
	} // end filter_method_name

} // end class

// TODO:	Update the instantiation call of your plugin to the name given at the class definition
$KWCUSGS = new KWCUSGS();
