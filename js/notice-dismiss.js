jQuery(document).ready(function() {
	jQuery("#retargeting-dismiss-notice").on('click', function() {
		sessionStorage.setItem('_ra_hidden', 'true');
	});

	var retSession = sessionStorage.getItem('_ra_hidden');
	if ( retSession  ) {
		jQuery("#retargeting-dismiss-notice").hide();
	}
});

