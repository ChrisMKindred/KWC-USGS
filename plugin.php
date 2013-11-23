<?php
/*
Plugin Name: USGS Steam Flow
Plugin URI: http://www.kindredwebconsulting.com
Description: Display USGS Stream Flow Data
Version: 0.01
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
	function __construct() {

		add_action( 'init', array( $this, 'plugin_textdomain' ) );
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );

		add_action( 'admin_menu', array( $this, 'setmenu' ));
		add_action( 'admin_init', array( $this, 'register_mysettings' ));

	    add_shortcode( "USGS", array( $this, 'USGS' ));
	    add_filter( 'widget_text', 'do_shortcode' );

		add_action( 'admin_footer', 'my_action_javascript' );
		
		add_action('wp_ajax_my_action', 'my_action_callback');

		function my_action_callback() {
			$state = $_POST['state'];

			$url = "http://waterservices.usgs.gov/nwis/iv?stateCd=$state&format=waterml&parameterCd=00060";
			$data = file_get_contents( $url );
			if ( !$data ){
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
			foreach ($xml_tree->timeSeries as $site_data){	
				$cnt = ++$cnt;
  				$page .= "<tbody>
							    <tr>
							      	<td>". $cnt ."</td>
							      	<td>". ucwords(strtolower($site_data->sourceInfo->siteName)) ."</td>
							      	<td>". $site_data->sourceInfo->siteCode ."</td>
							      	<td>". $site_data->sourceInfo->geoLocation->geogLocation->latitude ."</td>
							      	<td>". $site_data->sourceInfo->geoLocation->geogLocation->longitude ."</td>
							    </tr>";
			}
  			$page .= "</tbody></table>";
  			echo $page;
			die();
		}	
	} // end constructor

	public function activate( $network_wide ) {
	} // end activate

	public function deactivate( $network_wide ) {
	} // end deactivate

	public function uninstall( $network_wide ) {
	} // end uninstall

	public function plugin_textdomain() {
		$domain = 'kwc-usgs-locale';
		$locale = apply_filters( 'plugin_locale' , get_locale(), $domain );
        load_textdomain( $domain, WP_LANG_DIR.'/'.$domain.'/'.$domain.'-'.$locale.'.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	} // end plugin_textdomain

	public function register_admin_styles() {
		wp_enqueue_style( 'kwc-usgs-admin-styles', plugins_url( 'kwcusgs/css/admin.css' ) );
	} // end register_admin_styles

	public function register_admin_scripts() {
		wp_enqueue_script( 'kwc-usgs-admin-script', plugins_url( 'kwcusgs/js/admin.js' ), array('jquery') );

	} // end register_admin_scripts

	public function register_mysettings() { // whitelist options
  		register_setting( 'myoption-group', 'new_option_name' );
  		register_setting( 'myoption-group', 'some_other_option' );
  		register_setting( 'myoption-group', 'option_etc' );
	}

	public function register_plugin_styles() {
		wp_enqueue_style( 'kwc-usgs-plugin-styles', plugins_url( 'kwcusgs/css/display.css' ) );
	} // end register_plugin_styles

	public function register_plugin_scripts() {
		wp_enqueue_script( 'kwc-usgs-plugin-script', plugins_url( 'kwcusgs/js/display.js' ), array('jquery') );
	} // end register_plugin_scripts

	function USGS($atts, $content = null ) {
	   	extract( shortcode_atts( 
	   		array(
	   			'location' 	=> '09080400',
	   			'title'		=> null,
	   			'graph'		=> null
	   			), $atts ) );
	
		$url = "http://waterservices.usgs.gov/nwis/iv?site=$location&parameterCd=00010,00060,00065&format=waterml";
		
		$response = wp_remote_get( $url );
	  	$data = wp_remote_retrieve_body( $response );
	  	
	  	if ( ! $data ){
	   		return 'USGS Not Responding.';
	  	}

		$data = str_replace( 'ns1:', '', $data );

		$xml_tree = simplexml_load_string( $data );
		
		if ( False === $xml_tree ){
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
	  	foreach ( $xml_tree->timeSeries as $site_data ){	
	    	if ( $site_data->values->value == '' ) {
	      		$value = '-';
	    	} else if ( $site_data->values->value == -999999 ){
	      		$value = 'UNKNOWN';
	      		$provisional = '-';
	    	} else {
	      		$desc   = $site_data->variable->variableName;
	      		switch ( $site_data->variable->variableCode ) {
	        		case "00010":
	          			$value  = $site_data->values->value;
	          			$degf   = (9/5) * $value + 32;          			       
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
			          	break;

			        case "00065":
			          	$splitDesc = explode( ",", $desc );
			          	$value  = $site_data->values->value;
			          	$gageheight = $value;  
			          	$gageheightdesc = $splitDesc[1];          
	          			$thePage .= "<li class='gageheight'>Water Level: $gageheight $gageheightdesc</li>";
			          	break;	
	      		}
	    	}
	  	}
		$thePage .=		"</ul>";
		if ( isset( $graph ) ){
			if ( true == $graph ){
				$thePage .= "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00060'/>";
				$thePage .= "<img src='http://waterdata.usgs.gov/nwisweb/graph?site_no=$location&parm_cd=00065'/>";
			}
		}
		$thePage .= "<a href='http://waterdata.usgs.gov/nwis/uv?$location' target='_blank'>USGS</a>";
		$thePage .=	"</div>";
	   	return $thePage;
	}

	function setmenu() {
		add_submenu_page( 'options-general.php', 'Page title', 'USGS', 'manage_options', 'usgs', array( $this, 'my_magic_function' ) );
	}

	function my_magic_function(){
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$state_values=array(
                'AL' => "Alabama",
                'AK' => "Alaska",
                'AZ' => "Arizona",
                'AR' => "Arkansas",
                'CA' => "California",
                'CO' => "Colorado",
                'CT' => "Connecticut",
                'DE' => "Delaware",
                'DC' => "District Of Columbia",
                'FL' => "Florida",
                'GA' => "Georgia",
                'HI' => "Hawaii",
                'ID' => "Idaho",
                'IL' => "Illinois",
                'IN' => "Indiana",
                'IA' => "Iowa",
                'KS' => "Kansas",
                'KY' => "Kentucky",
                'LA' => "Louisiana",
                'ME' => "Maine",
                'MD' => "Maryland",
                'MA' => "Massachusetts",
                'MI' => "Michigan",
                'MN' => "Minnesota",
                'MS' => "Mississippi",
                'MO' => "Missouri",
                'MT' => "Montana",
                'NE' => "Nebraska",
                'NV' => "Nevada",
                'NH' => "New Hampshire",
                'NJ' => "New Jersey",
                'NM' => "New Mexico",
                'NY' => "New York",
                'NC' => "North Carolina",
                'ND' => "North Dakota",
                'OH' => "Ohio",
                'OK' => "Oklahoma",
                'OR' => "Oregon",
                'PA' => "Pennsylvania",
                'RI' => "Rhode Island",
                'SC' => "South Carolina",
                'SD' => "South Dakota",
                'TN' => "Tennessee",
                'TX' => "Texas",
                'UT' => "Utah",
                'VT' => "Vermont",
                'VA' => "Virginia",
                'WA' => "Washington",
                'WV' => "West Virginia",
                'WI' => "Wisconsin",
                'WY' => "Wyoming"
    );

	function listUSStates( $state_values, $dropdown_name, $key_selected ) {
	    $string = "<select id='state' class='state' name=\"" . $dropdown_name . "\">\n";
    	if ( !empty( $state_values ) ) {
        	if ( $key_selected == "" || !isset( $key_selected ) ) {
            	$string .= "<option value=\"\"></option>\n";
        	}
        	foreach( $state_values as $state_short=>$state_full ) {
            	if ( $key_selected != "" && $key_selected == $state_short ) {
                	$additional = " SELECTED";
            	} else {
                	$additional = "";
            	}
                $string.="<option value=\"" . $state_short . "\"" . $additional . ">" . $state_full . "</option>\n";
        	}
    	}
    	$string .= "</select>\n";
    	return $string;
	}
	function my_action_javascript() {
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

	?>
		<div class="wrap">
			<?php screen_icon(); ?> <h2>USGS Stream Flow Data</h2>
			<h3>Search Site Codes</h3>
			<label>Select State</label>
			<?php 
				$select_box_name = "state";
				$key_selected = "";
				echo listUSStates( $state_values, $select_box_name, $key_selected );
			?>
			<input type='submit' value='<?php _e('Get Stations'); ?>' class='button-secondary' />
	    	<br /><br />

	    	<div class="" id="results"></div>
	    </div>
	    <?php 	
	}

	function filter_method_name() {
	} // end filter_method_name

} // end class

$KWCUSGS = new KWCUSGS();
