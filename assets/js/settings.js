document.getElementById( 'prpl-settings-form' ).addEventListener( 'submit', function( event ) {
	event.preventDefault();
	const form = new FormData( this );
	const data = form.getAll( 'prpl-settings-post-types-exclude[]' );

	// Save the options.
	progressPlannerAjaxRequest( {
		url: progressPlanner.ajaxUrl,
		data: {
			action: 'progress_planner_save_cpt_settings',
			_ajax_nonce: progressPlanner.nonce,
			include_post_types: data,
		}
	} );

	document.getElementById( 'submit-exclude-post-types' ).disabled = true;
	document.getElementById( 'submit-exclude-post-types' ).innerHTML = progressPlanner.l10n.saved;
} );
