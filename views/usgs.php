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
	<ul class='sitevalues'>
	<?php
	if ( isset( $args['watertemp'] ) && count( $args['watertemp'] ) > 0 ) {
		$watertemp = $args['watertemp'];
		echo sprintf(
			"<li class='%s'>%s: %s %s</li>",
			esc_attr( $watertemp['class'] ),
			esc_html( $watertemp['name'] ),
			esc_html( $watertemp['value'] ),
			esc_html( $watertemp['description'] )
		);
	}
	if ( isset( $args['flow'] ) && count( $args['flow'] ) > 0 ) {
		$flow = $args['flow'];
		echo sprintf(
			"<li class='%s'>%s: %s %s</li>",
			esc_attr( $flow['class'] ),
			esc_html( $flow['name'] ),
			esc_html( $flow['value'] ),
			esc_html( $flow['description'] )
		);
	}
	if ( isset( $args['gageheight'] ) && count( $args['gageheight'] ) > 0 ) {
		$gageheight = $args['gageheight'];
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
	<?php if ( isset( $args['graph'] ) ) { ?>
		<div class='clearfix'>
			<?php if ( isset( $args['watertemp']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['watertemp']['graph'] ); ?>' alt='<?php echo esc_attr( $args['watertemp']['name'] . ' Graph' ); ?>' />
			<?php } ?>
			<?php if ( isset( $args['flow']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['flow']['graph'] ); ?>' alt='<?php echo esc_attr( $args['flow']['name'] . ' Graph' ); ?>' />
			<?php } ?>
			<?php if ( isset( $args['gageheight']['graph'] ) ) { ?>
				<img src='<?php echo esc_url( $args['gageheight']['graph'] ); ?>' alt='<?php echo esc_attr( $args['gageheight']['name'] . ' Graph' ); ?>' />
			<?php } ?>
		</div>
	<?php } ?>
	<a class='clearfix' href='https://waterdata.usgs.gov/nwis/uv?<?php echo esc_attr( $args['location'] ); ?>' target='_blank'>USGS</a>
</div>
