<?php
/*
Plugin Name: USGS
Plugin URL: http://usgs.kindredwebconsulting.com/
Description: Loads USGS Location information.
Version: 0.01
Author: Chris Kindred
Author URI: http://www.kindredwebconsulting.com/
*/



require_once( KWC_USGS_PLUGIN_PATH . 'includes/classes.php' );
define( 'KWC_USGS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

//Make shortcodes work in widget area
add_filter('widget_text', 'do_shortcode');

add_shortcode("USGS", "KWC_USGS");

function KWC_USGS($atts, $content = null ) {
   	extract( shortcode_atts( array('location' => '09080400'), $atts ) );
	
	$url = "http://waterservices.usgs.gov/nwis/iv?site=$location&parameterCd=00010,00060,00065&format=waterml";
	
	$data = file_get_contents($url);
  	
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
	$SiteName = $xml_tree->timeSeries->sourceInfo->siteName;
	$latitude  = (double) $xml_tree->timeSeries->sourceInfo->geoLocation->geogLocation->latitude; //North
	$longitude = (double) $xml_tree->timeSeries->sourceInfo->geoLocation->geogLocation->longitude; //West
	$time_format = 'h:i:s A';
	$gageheight   = "";
  	$watertemp    = "";
  	$streamflow   = "";
  	$gageheightdesc   = "";
  	$watertempdesc    = "";
  	$streamflowdesc   = "";

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
          			break;
		        case "00060":
		          	$splitDesc = explode(",",$desc);
		          	$value  = $site_data->values->value;
		          	$streamflow     = $value;
		          	$streamflowdesc = $splitDesc[1];
		          	break;
		        case "00065":
		          	$splitDesc = explode(",",$desc);
		          	$value  = $site_data->values->value;
		          	$gageheight = $value;  
		          	$gageheightdesc = $splitDesc[1];          
		          	break;	
      		}
    	}
  	}
		
	$thePage = "<div class='KWC_USGS'>
					<h1>$SiteName</h1>
					<ul>
						<li>Steam Flow: $streamflow $streamflowdesc</li>
						<li>Water Temp: $watertemp $watertempdesc</li>
						<li>Gage Height: $gageheight $gageheightdesc</li>
					</ul>
				</div>";
   	return $thePage;
}