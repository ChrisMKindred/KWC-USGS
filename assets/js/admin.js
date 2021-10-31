$j=jQuery.noConflict();
$j(document).ready(function() {
	$j('.button-secondary').click(function() {
		$j( "#results" ).html("Loading Stations...")
		var data = {
			action: 'kwcusgsajax',
			state: $j( ".state" ).val()
		};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$j.post(ajaxurl, data, function(response) {
			$j( "#results" ).html( response );
		});
	});
});
