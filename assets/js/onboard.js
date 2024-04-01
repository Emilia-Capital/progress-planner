/* global progressPlanner */

/**
 * Make the AJAX request.
 *
 * @param {Object} data The data to send with the request.
 */
const progressPlannerAjaxAPIRequest = ( data ) => {
	progressPlannerAjaxRequest( {
		method: 'POST',
		url: progressPlanner.onboardAPIUrl,
		data: data,
		successAction: ( response ) => {
			// Make a local request to save the response data.
			progressPlannerAjaxRequest( {
				method: 'POST',
				url: progressPlanner.ajaxUrl,
				data: {
					action: 'progress_planner_save_onboard_data',
					_ajax_nonce: progressPlanner.nonce,
					key: response.license_key,
				},
				successAction: ( response ) => {
					// Start scanning posts.
					document.querySelector( '#progress-planner-onboard-responses .scanning-posts' ).style.display = 'list-item';
					progressPlannerTriggerScan();
					// TODO: Print a link in the UI so the user can directly go to change their password.
					console.log( response );
				},
			} );
		},
		failAction: ( response ) => {
			console.log( response );
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
	document.querySelector( '#progress-planner-onboard-responses .registering-site' ).style.display = 'list-item';
	progressPlannerAjaxRequest( {
		method: 'POST',
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
