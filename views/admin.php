<?php
/**
 * The admin view file for plugin
 *
 * @package   USGS Steam Flow Data
 * @author    Chris Kindred <Chris@kindredwebconsulting.com>
 * @license   GPL-2.0+
 * @link      //www.kindredwebconsulting.com
 */

use Kindred\USGS\Core;

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'kwc_usgs' ) );
}

$usgs_tabs = [
	'home-settings' => __( 'Usage', 'kwc_usgs' ),
	'search'        => __( 'Search Site Codes', 'kwc_usgs' ),
];

$usgs_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'home-settings';
?>
<div class="wrap">
	<h2><span style="padding-top: 5px;" class="dashicons dashicons-location"></span> <?php echo esc_html( get_admin_page_title() ); ?> </h2><br />
	<h2 class="nav-tab-wrapper">
	<?php
	foreach ( $usgs_tabs as $usgs_tab => $usgs_name ) {
		$usgs_class = ( $usgs_tab == $usgs_active_tab ) ? ' nav-tab-active' : '';
		echo sprintf(
			'<a class="nav-tab%s" href="?page=%s&tab=%s">%s</a>',
			$usgs_class,
			Core::PLUGIN_NAME,
			$usgs_tab,
			$usgs_name
		);
	}
	?>
	</h2>
	<?php
	switch ( $usgs_active_tab ) {
		case 'search':
			$usgs_state_values = [
				'AL' => 'Alabama',
				'AK' => 'Alaska',
				'AZ' => 'Arizona',
				'AR' => 'Arkansas',
				'CA' => 'California',
				'CO' => 'Colorado',
				'CT' => 'Connecticut',
				'DE' => 'Delaware',
				'DC' => 'District Of Columbia',
				'FL' => 'Florida',
				'GA' => 'Georgia',
				'HI' => 'Hawaii',
				'ID' => 'Idaho',
				'IL' => 'Illinois',
				'IN' => 'Indiana',
				'IA' => 'Iowa',
				'KS' => 'Kansas',
				'KY' => 'Kentucky',
				'LA' => 'Louisiana',
				'ME' => 'Maine',
				'MD' => 'Maryland',
				'MA' => 'Massachusetts',
				'MI' => 'Michigan',
				'MN' => 'Minnesota',
				'MS' => 'Mississippi',
				'MO' => 'Missouri',
				'MT' => 'Montana',
				'NE' => 'Nebraska',
				'NV' => 'Nevada',
				'NH' => 'New Hampshire',
				'NJ' => 'New Jersey',
				'NM' => 'New Mexico',
				'NY' => 'New York',
				'NC' => 'North Carolina',
				'ND' => 'North Dakota',
				'OH' => 'Ohio',
				'OK' => 'Oklahoma',
				'OR' => 'Oregon',
				'PA' => 'Pennsylvania',
				'RI' => 'Rhode Island',
				'SC' => 'South Carolina',
				'SD' => 'South Dakota',
				'TN' => 'Tennessee',
				'TX' => 'Texas',
				'UT' => 'Utah',
				'VT' => 'Vermont',
				'VA' => 'Virginia',
				'WA' => 'Washington',
				'WV' => 'West Virginia',
				'WI' => 'Wisconsin',
				'WY' => 'Wyoming',
			];
			?>
			<h3>Search Site Codes</h3>
			<p class="">You can search for a site by selecting a state and pressing the 'Get Stations' button.  The stations come up in code order directly from USGS.  Once you are showing a states stations the easiest way to find the one you are looking for is to use the browswers built in search.  The search can take as long as two minutes to contact USGS and bring back the data in some cases.</p>
			<p class="">Click the Site Name link to go directly to the site on the USGS website.</p>
			<p class="">Click the Latitude / Longitude link to view the site location in google maps.  The location in google maps is an aproximation.</p>
			<label>Select State:</label>
			<?php
			echo "<select id='state' class='state' name='state'>";
			$usgs_blnfirst = true;
			foreach ( $usgs_state_values as $usgs_state_short => $usgs_state_full ) {
				if ( true == $usgs_blnfirst ) {
					echo "<option value='$usgs_state_short' SELECTED >$usgs_state_full</option>";
					$usgs_blnfirst = false;
				} else {
					echo "<option value='$usgs_state_short' >$usgs_state_full</option>";
				}
			}
			echo '</select>';
			?>
			<input type='submit' value='<?php _e( 'Get Stations', 'kwc_usgs' ); ?>' class='button-secondary' />
			<br /><br />
			<div class="" id="results"></div>
			<?php
			break;

		default:
			?>
			<h3 class="">Using USGS Steam Flow Data</h3>
			<p class="">This plugin uses short codes to allow you to include the USGS infromation for a location on any post or in a Text Widget.</p>
			<p class="">You can copy and paste the shortcode below to get started.</p>
			<p><code>[USGS location='09080400' title='Great Place To Fish' graph='show' /]</code></p>
			<h4 class="">Location</h4>
			<p>The location is the Site Code for the location you want to show. You can get the Site Code by using the Search tab or by finding it on the USGS website.</p>
			<h4 class="">Title</h4>
			<p>The title is what you would like to use as a title for the location information.  The title defaults to the Site Name if you leave it blank. </p>
			<h4 class="">Graph</h4>
			<p>Graph is for showing graphs with the information (true) or not (false).  If you leave Graph blank it will default to false.</p>
			<h2 class="">USGS</h2>
			<p class="">The plugin is dependent on the <a href="//www.usgs.gov/water/">USGS website</a> for the data that is being displayed.</p>
			<a href="//www.usgs.gov/water/"><img src="<?php echo esc_url( USGS_URL . '/assets/img/usgs.jpg' ); ?>" alt="USGS" border="0" height="32px" /></a>
			<h2 class="">Support</h2>
			<p class="">Thanks for using the pluging. If you need support, please submit your question on the plugins <a href="https://wordpress.org/support/plugin/usgs-stream-flow-data/" target="_blank" >support forum on WordPress.org</a>.  You can also submit an issue on <a href="https://github.com/ChrisMKindred/KWC-USGS/" target="_blank">GitHub</a>.</p>
			<?php
			break;
	}
	?>
</div>
