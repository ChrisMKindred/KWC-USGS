<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   USGS Steam Flow
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      http://www.kindredwebconsulting.com
 * @copyright 2013 Kindred Web Consulting
 */
?>

<div class="wrap">

	<?php screen_icon(); ?>
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php
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
	?>
			<h3>Search Site Codes</h3>
			<label>Select State</label>
			<?php 
	    		echo "<select id='state' class='state' name='$dropdown_name'>";
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
</div>

