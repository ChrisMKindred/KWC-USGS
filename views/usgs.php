<div class='KWC_USGS clearfix'>
	<h3 class='header'><?php esc_html_e( $args['title'], 'kwcusgs' ); ?></h3>
	<ul class='sitevalues'>
	<?php
		if ( isset( $args['watertemp'] ) && count( $args['watertemp'] ) > 0 ) {
			$watertemp = $args['watertemp'];
			echo sprintf( "<li class='%s'>%s: %s %s</li>",
				esc_attr( $watertemp['class'] ),
				esc_html__( $watertemp['name'], 'kwcusgs' ),
				esc_html( $watertemp['value'] ),
				esc_html( $watertemp['description'] ),
			);
		}
		if ( isset( $args['flow'] ) && count( $args['flow'] ) > 0 ) {
			$flow = $args['flow'];
			echo sprintf( "<li class='%s'>%s: %s %s</li>",
				esc_attr( $flow['class'] ),
				esc_html__( $flow['name'], 'kwcusgs' ),
				esc_html( $flow['value'] ),
				esc_html( $flow['description'] ),
			);
		}
		if ( isset( $args['gageheight'] ) && count( $args['gageheight'] ) > 0 ) {
			$gageheight = $args['gageheight'];
			echo sprintf( "<li class='%s'>%s: %s %s</li>",
				esc_attr( $gageheight['class'] ),
				esc_html__( $gageheight['name'], 'kwcusgs' ),
				esc_html( $gageheight['value'] ),
				esc_html( $gageheight['description'] ),
			);
		}
	?>
	</ul>
	<?php if ( $args['graph'] ) { ?>
		<div class='clearfix'>
			<?php if ( isset( $args['watertemp']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['watertemp']['graph'] ); ?>' alt='<?php esc_attr_e( $args['watertemp']['name'] . ' Graph', 'kwcusgs' ); ?>' />
			<?php } ?>
			<?php if ( isset( $args['flow']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['flow']['graph'] ); ?>' alt='<?php esc_attr_e( $args['flow']['name'] . ' Graph', 'kwcusgs' ); ?>' />
			<?php } ?>
			<?php if ( isset( $args['gageheight']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['gageheight']['graph'] ); ?>' alt='<?php esc_attr_e( $args['gageheight']['name'] . ' Graph', 'kwcusgs' ); ?>' />
			<?php } ?>
		</div>
	<?php } ?>
	<a class='clearfix' href='https://waterdata.usgs.gov/nwis/uv?<?php echo esc_attr( $args['location'] ); ?>' target='_blank'>USGS</a>
</div>
