import $ from 'jquery'

$(document).ready(function() {
	$('.button-secondary').click(function() {
		$( "#results" ).html("Loading Stations...")
		var data = {
			action: 'kwcusgsajax',
			state: $( ".state" ).val()
		};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(ajaxurl, data, function(response) {
			$( "#results" ).html( response );
		});
	});
});
