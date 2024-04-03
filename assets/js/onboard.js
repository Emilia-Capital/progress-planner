/* global progressPlanner, progressPlannerAjaxRequest */

/**
 * Make a request to save the license key.
 *
 * @param {string} key The license key.
 */
const progressPlannerSaveLicenseKey = ( licenseKey ) => {
	progressPlannerAjaxRequest( {
		url: progressPlanner.ajaxUrl,
		data: {
			action: 'progress_planner_save_onboard_data',
			_ajax_nonce: progressPlanner.nonce,
			key: licenseKey
		},
	} );
}

/**
 * Make the AJAX request.
 *
 * @param {Object} data The data to send with the request.
 */
const progressPlannerAjaxAPIRequest = ( data ) => {
	progressPlannerAjaxRequest( {
		url: progressPlanner.onboardAPIUrl,
		data: data,
		successAction: ( response ) => {

			// Show link to reset password.
			document.getElementById( 'prpl-password-reset-link' ).style.display = 'block';
			document.getElementById( 'prpl-password-reset-link' ).href = response.password_reset_url;

			// Hide the form.
			document.getElementById( 'prpl-onboarding-form' ).style.display = 'none';

			// Make a local request to save the response data.
			progressPlannerSaveLicenseKey( response.license_key );

			// Start scanning posts.
			progressPlannerTriggerScan();
		},
		failAction: ( response ) => {
			console.warn( response );
		},
	} );
};

/**
 * Make the AJAX request.
 *
 * Make a request to get the nonce.
 * Once the nonce is received, make a request to the API.
 *
 * @param {Object} data The data to send with the request.
 */
const progressPlannerOnboardCall = ( data ) => {
	progressPlannerAjaxRequest( {
		url: progressPlanner.onboardNonceURL,
		data: data,
		successAction: ( response ) => {
			if ( 'ok' === response.status ) {

				// Add the nonce to our data object.
				data.nonce = response.nonce;

				// Make the request to the API.
				progressPlannerAjaxAPIRequest( data );
			}
		},
	} );
};

if ( document.getElementById( 'prpl-onboarding-form' ) ) {
	document.getElementById( 'prpl-onboarding-form' ).addEventListener( 'submit', function( event ) {
		event.preventDefault();
		document.querySelector( '#prpl-onboarding-form input[type="submit"]' ).disabled = true;
		const inputs = this.querySelectorAll( 'input' );

		// Build the data object.
		const data   = {};
		inputs.forEach( input => {
			if ( input.name ) {
				data[ input.name ] = input.value;
			}
		} );
		progressPlannerOnboardCall( data );
	} );
}
