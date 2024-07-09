/* global progressPlannerAjaxRequest, progressPlanner */
document
	.getElementById( 'prpl-settings-form' )
	.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		const form = new FormData( this );
		const data = form.getAll( 'prpl-settings-post-types-include[]' );

		// Save the options.
		progressPlannerAjaxRequest( {
			url: progressPlanner.ajaxUrl,
			data: {
				action: 'progress_planner_save_cpt_settings',
				_ajax_nonce: progressPlanner.nonce,
				include_post_types: data,
			},
			successAction: () => {
				window.location.reload();
			},
		} );

		document.getElementById( 'submit-include-post-types' ).disabled = true;
		document.getElementById( 'submit-include-post-types' ).innerHTML =
			progressPlanner.l10n.saving;
	} );
