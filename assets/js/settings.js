/* global prpl, prplAjaxRequest, prplSaveLicenseKey */
document
	.getElementById( 'prpl-settings-form' )
	.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		const form = new FormData( this );
		const data = form.getAll( 'prpl-settings-post-types-include[]' );

		// Save the options.
		const request = wp.ajax.post( 'prpl_save_cpt_settings', {
			_ajax_nonce: prpl.nonce,
			include_post_types: data.join( ',' ),
		} );
		request.done( () => {
			window.location.reload();
		} );

		document.getElementById( 'submit-include-post-types' ).disabled = true;
		document.getElementById( 'submit-include-post-types' ).innerHTML =
			prpl.l10n.saving;
	} );

// Submit the email.
const settingsLicenseForm = document.getElementById(
	'prpl-settings-license-form'
);
if ( !! settingsLicenseForm ) {
	settingsLicenseForm.addEventListener( 'submit', function ( event ) {
		event.preventDefault();
		const form = new FormData( this );
		const data = {};

		// Build the onboarding data object.
		for ( const [ key, value ] of form.entries() ) {
			data[ key ] = value;
		}

		prplAjaxRequest( {
			url: prpl.onboardNonceURL,
			data,
			successAction: ( response ) => {
				if ( 'ok' === response.status ) {
					// Add the nonce to our data object.
					data.nonce = response.nonce;

					// Make the request to the API.
					prplAjaxRequest( {
						url: prpl.onboardAPIUrl,
						data,
						successAction: ( apiResponse ) => {
							// Make a local request to save the response data.
							prplSaveLicenseKey(
								apiResponse.license_key
							);

							document.getElementById(
								'submit-license-key'
							).innerHTML = prpl.l10n.subscribed;

							// Timeout so the license key is saved.
							setTimeout( () => {
								// Reload the page.
								window.location.reload();
							}, 500 );
						},
						failAction: ( apiResponse ) => {
							// eslint-disable-next-line no-console
							console.warn( apiResponse );
						},
					} );
				}
			},
		} );

		document.getElementById( 'submit-license-key' ).disabled = true;
		document.getElementById( 'submit-license-key' ).innerHTML =
			prpl.l10n.subscribing;
	} );
}
