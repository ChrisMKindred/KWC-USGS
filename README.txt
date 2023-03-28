=== USGS Steam Flow Data ===
Contributors: ChrisMKindred
Donate link: //www.kindredwebconsulting.com/wp-plugins/usgs
Tags: USGS, River Flow, Stream Flow, Fly Fishing, Water Level
Requires at least: 5.5
Tested up to: 6.2
Requires PHP: 7.0
Stable tag: 23.03.01
License: GPLv2 or later
License URI: //www.gnu.org/licenses/gpl-2.0.html

This plugin uses shortcodes so you can get the USGS river flow data for a site location.  It also includes a easy to use Site Code Search.

== Description ==

This plugin allows you to use a shortcode to display the USGS River Data for a site location.  The shortcode can be included in Posts, Pages and Text Widgets.

The shortcode allows you to set your own title and whether or not to show a graph with it.

Example Shortcode:
[USGS location='09080400' title='Great Place To Fish' graph='show']

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'USGS Steam Flow Data'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `usgs-stream-flow-data.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `usgs-stream-flow-data.zip`
2. Extract the `usgs-stream-flow-data.zip` directory to your computer
3. Upload the `usgs-stream-flow-data.zip` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard

== Frequently Asked Questions ==

= How do I use the short code? =

Enter [USGS location='09080400' graph='show' title='The Title'].  The location is the Site Code for the station, graph is set to 'show' to show the graphs, the title is what you want to display as a title.  If this is blank it will display the location name.

= How do I find the Site Code? =

You can find the Site Code quickly and easily by searching for it based on state in the easy to use Site Code Search tab on the plugin settings page.

= Why is the data not updating? =

There is a built in cache for the data.  The data will update every 15 minutes in order to help with site speed and limit the calls to USGS.

== Screenshots ==

1. Site Code Search By State
2. Admin Page
3. Showing USGS Data as a Widget
4. Showing USGS Without Graphs in a Post

== Changelog ==

= 22.05.01 =
- Added `kwc_usgs_before_sitevalues_list` action before sitevalues list.
- Added `kwc_usgs_after_sitevalues_list` action after sitevalues list.
- Added `kwc_usgs_water_temp`, `kwc_usgs_flow`, `kwc_usgs_gageheight` filters.
- Tested up to WP Core 6.0.

= 22.04.01 =
adds:
- Tested with 5.9.2
- Code Analysis testing for cleaner code and less potential errors.

fixes:
- An error in the shortcode when a location is not found.

= 21.11.04 =
fixes: Error where you couldn't have multiple shortcodes on a page.

= 21.11.03 =
adds: version bump.

= 21.11.02 =
removes: .wordpress-org from deployment

= 21.11.01 =
adds: refactor the majority of the plugin to allow for easier changes in the future.
adds: The ability to change the template for the shortcode by includeing a usgs.php file in your theme or plugin.
adds: tested up to 5.8.1.
removes: WP Support is now 5.5 and up.
removes: PHP compatibility is now 7.0 and up.

= 21.05.01 =
Tested up to 5.7.2.
Update transient to limit key length.

= 20.08.02 =
Update admin remote call to allow for 45 seconds before timeout.

= 20.08.01 =
Tested plugin up to 5.5.
Updated versioning system to CalVer (https://calver.org/).

= 2.8 =
Tested plugin up to 5.3.

= 2.7.1 =
Adds additional tests and allows for more trusted call to API Url.

= 2.7 =
Tested with WordPress 4.9.8

= 2.6 =
Update to build process to allow for Travis-ci.org integration and unit testing

= 2.5.1 =
Fixes error with Graph Loading

= 2.5 =
Updated URL Fetch to use the `wp_remote_get()` function
Tested up to 4.9.4

= 2.4.2 =
Updated url for location search

= 2.4.1 =
SSL Fix for Graphs

= 2.4 =
Tested up to 4.5

= 2.3 =
Tested up to 4.3

= 2.2.1 =
Tested Up To 4.2.3

= 2.2 =
Tested Up To 4.2

= 2.1 =
Fixed issue with graphs showing up if there is no data

= 2.0.1 =
Fixed issue with Temp Conversion
Tested with WP 4.1.1
Updated KWC Logo

= 2.0.0 =
Tested with 4.0
Removed old MP5 references
Setup cache busting for images
Limited calls to USGS to every 15 minutes

= 1.0.7 =
Tested with 3.9
Updated plugin Description to fit on WordPress.org
Updated ScreenShots to match 3.9

= 1.0.4 =
Updated Plugin Description

= 1.0.3 =
Tested with 3.9

= 1.0.2 =
Updated icons to match new admin UI
Tested plugin with 3.8

= 1.0.1 =
Updated the zip file name to match WordPress created zip file.

= 1.0.0 =
Starting Version

== Upgrade Notice ==
= 1.0.7 =
Tested with 3.9
Updated plugin Description to fit on WordPress.org
Updated ScreenShots to match 3.9

= 1.0.4 =
Verifies compatibility with Core 3.9
Update to plugin Description

= 1.0.3 =
Verifies compatibility with Core 3.9

= 1.0.2 =
Verifies compatibility with Core 3.8

= 1.0.1 =
Updated the zip file name to match WordPress created zip file.

= 1.0.0 =
Starting Version
