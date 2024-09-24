/* global progressPlanner */
document
	.getElementById( 'prpl-settings-form' )
	.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		const form = new FormData( this );
		const data = form.getAll( 'prpl-settings-post-types-include[]' );

		// Save the options.
		const request = wp.ajax.post( 'progress_planner_save_cpt_settings', {
			_ajax_nonce: progressPlanner.nonce,
			include_post_types: data,
		} );
		request.done( () => {
			window.location.reload();
		} );

		document.getElementById( 'submit-include-post-types' ).disabled = true;
		document.getElementById( 'submit-include-post-types' ).innerHTML =
			progressPlanner.l10n.saving;
	} );
