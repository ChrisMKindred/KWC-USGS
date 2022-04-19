<?php
/**
 * @var array{
 *	'title': string,
 *	'graph'?: string,
 *  'watertemp'?: array<string>,
 *  'flow'?: array<string>,
 *  'gageheight'?: array<string>,
 *	'location': string,
 * } $args
 */
?>
<div class='KWC_USGS clearfix'>
	<h3 class='header'><?php echo esc_html( $args['title'] ); ?></h3>

	<?php
	/**
	 * Allows for additional output before the sitevalues list.
	 *
	 * @since 22.04.02
	 * @hook kwc_usgs_before_sitevalues_list
	 *
	 * @param array $args An associative array of the data used to build the listing.
	 */
	do_action( 'kwc_usgs_before_sitevalues_list', $args );
	?>

	<ul class='sitevalues'>
	<?php
	if ( isset( $args['watertemp'] ) && count( $args['watertemp'] ) > 0 ) {

		/**
		 * Filters the water temp array.
		 *
		 * @since 22.04.02
		 * @hook kwc_usgs_water_temp
		 *
		 * @param array		$watertemp  Associative array of the water temp data [ `class`, `name`, `value`, `description`, `graph` ].
		 * @param string	$location   The USGS location.
		 *
		 * @return array	Associative array of the water temp data [ `class`, `name`, `value`, `description`, `graph` ].
		 */
		$watertemp = apply_filters( 'kwc_usgs_water_temp', $args['watertemp'], $args['location'] );

		echo sprintf(
			"<li class='%s'>%s: %s %s</li>",
			esc_attr( $watertemp['class'] ),
			esc_html( $watertemp['name'] ),
			esc_html( $watertemp['value'] ),
			esc_html( $watertemp['description'] )
		);
	}

	if ( isset( $args['flow'] ) && count( $args['flow'] ) > 0 ) {

		/**
		 * Filters the Flow array.
		 *
		 * @since 22.04.02
		 * @hook kwc_usgs_flow
		 *
		 * @param array		$flow  		Associative array of the flow data [ `class`, `name`, `value`, `description`, `graph` ].
		 * @param string	$location   The USGS location.
		 *
		 * @return array	Associative array of the flow data [ `class`, `name`, `value`, `description`, `graph` ].
		 */
		$flow = apply_filters( 'kwc_usgs_flow', $args['flow'], $args['location'] );

		echo sprintf(
			"<li class='%s'>%s: %s %s</li>",
			esc_attr( $flow['class'] ),
			esc_html( $flow['name'] ),
			esc_html( $flow['value'] ),
			esc_html( $flow['description'] )
		);
	}

	if ( isset( $args['gageheight'] ) && count( $args['gageheight'] ) > 0 ) {

		/**
		 * Filters the Gage Height array.
		 *
		 * @since 22.04.02
		 * @hook kwc_usgs_gageheight
		 *
		 * @param array		$gageheight	Associative array of the gageheight data [ `class`, `name`, `value`, `description`, `graph` ].
		 * @param string	$location	The USGS location.
		 *
		 * @return array	Associative array of the gageheight data [ `class`, `name`, `value`, `description`, `graph` ].
		 */
		$gageheight = apply_filters( 'kwc_usgs_gageheight', $args['gageheight'], $args['location'] );

		echo sprintf(
			"<li class='%s'>%s: %s %s</li>",
			esc_attr( $gageheight['class'] ),
			esc_html( $gageheight['name'] ),
			esc_html( $gageheight['value'] ),
			esc_html( $gageheight['description'] )
		);
	}
	?>
	</ul>

	<?php
	/**
	 * Filters the taxonomies that should be synced.
	 *
	 * @since 22.04.02
	 * @hook kwc_usgs_after_sitevalues_list
	 *
	 * @param array	$args	An array of the data used to build the listing.
	 */
	do_action( 'kwc_usgs_after_sitevalues_list', $args );

	if ( isset( $args['graph'] ) ) {
		?>
		<div class='clearfix'>
			<?php
			if ( isset( $args['watertemp']['graph'] ) ) {
				echo sprintf(
					'<img src="%s" alt="%s" />',
					esc_url( $args['watertemp']['graph'] ),
					esc_attr( $args['watertemp']['name'] . ' ' . __( 'Graph', 'kwc_usgs' ) )
				);
			}

			if ( isset( $args['flow']['graph'] ) ) {
				echo sprintf(
					'<img src="%s" alt="%s" />',
					esc_url( $args['flow']['graph'] ),
					esc_attr( $args['flow']['name'] . ' ' . __( 'Graph', 'kwc_usgs' ) )
				);
			}

			if ( isset( $args['gageheight']['graph'] ) ) {
				echo sprintf(
					'<img src="%s" alt="%s" />',
					esc_url( $args['gageheight']['graph'] ),
					esc_attr( $args['gageheight']['name'] . ' ' . __( 'Graph', 'kwc_usgs' ) )
				);
			} ?>
		</div>
	<?php } ?>
	<a class='clearfix' href='https://waterdata.usgs.gov/nwis/uv?<?php echo esc_attr( $args['location'] ); ?>' target='_blank'>USGS</a>
</div>
