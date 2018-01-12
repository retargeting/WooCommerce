jQuery(document).ready(function() {
	jQuery("#retargeting-dismiss-notice").on('click', function() {
		sessionStorage.setItem('hidden', 'true');
	});

	var retSession = sessionStorage.getItem('hidden');
	if ( retSession  ) {
		jQuery("#retargeting-dismiss-notice").hide();
	}
});

