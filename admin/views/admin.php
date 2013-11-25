<?php
/**
 * @package   USGS Steam Flow Data
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      http://www.kindredwebconsulting.com
 * @copyright 2013 Kindred Web Consulting
 */
?>
<?php  
if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
$tabs = array( 'home-settings' => 'Usage', 'search' => 'Search Site Codes', 'credits' => 'Credits' );
$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home-settings';  
?>
<div class="wrap">

	<div id="icon-plugins" class="icon32"><br></div>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <div id="icon-options-general" class="icon32"><br></div>
    <h2 class="nav-tab-wrapper"> 
    <?php 
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $active_tab ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=kwcusgs&tab=$tab'>$name</a>";
    }
    ?>
    </h2>  	
    <?php

        switch ( $active_tab ){
            case 'search':
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
    ?>
            <h3>Search Site Codes</h3>
            <p class="">You can search for a site by selecting a state and pressing the 'Get Stations' button.  The stations come up in code order directly from USGS.  Once you are showing a states stations the easiest way to find the one you are looking for is to use the browswers built in search.  The search can take as long as two minutes to contact USGS and bring back the data in some cases.</p>
            <p class="">Click the Site Name link to go directly to the site on the USGS website.</p>
            <p class="">Click the Latitude / Longitude link to view the site location in google maps.  The location in google maps is an aproximation.</p>
            <label>Select State:</label>
            <?php 
                echo "<select id='state' class='state' name='state'>";
                if ( !empty( $state_values ) ) {
                    $blnfirst = true;
                    foreach( $state_values as $state_short => $state_full ) {
                        if ( true == $blnfirst ){
                            echo "<option value='$state_short' SELECTED >$state_full</option>";
                            $blnfirst = false;
                        } else {
                            echo "<option value='$state_short' >$state_full</option>";
                        }
                    }
                }
                echo "</select>";
            ?>
            <input type='submit' value='<?php _e('Get Stations'); ?>' class='button-secondary' />
            <br /><br />
            <div class="" id="results"></div>
<?php
                break;

            case 'home-settings':
?>
                <h3 class="">Using USGS Fly Fishing Steam Flow</h3>
                <p class="">This plugin uses short codes to allow you to include the USGS infromation for a location on any post or in a Text Widget.</p>
                <p class="">You can copy and paste the shortcode below to get started.<br />
                <br />
                [USGS location='09080400' title='Great Place To Fish' graph='show'] <br />
                <br />
                <h4 class="">location</h4>
                The location is the Site Code for the location you want to show. You can get the Site Code by using the Search tab or by finding it on the USGS website.<br />
                <h4 class="">Title</h4>
                The title is what you would like to use as a title for the location information.  The title defaults to the Site Name if you leave it blank. <br />
                <h4 class="">Graph</h4>
                Graph is for showing graphs with the information (true) or not (false).  If you leave Graph blank it will default to false.
 </p>
<?php
                break;

            default:
                echo 'Kindred Web Consulting';
                break;
        } 
?>
</div>