/* global progressPlanner, progressPlannerAjaxRequest, progressPlannerTriggerScan */

/**
 * Make a request to save the license key.
 *
 * @param {string} licenseKey The license key.
 */
const progressPlannerSaveLicenseKey = ( licenseKey ) => {
	// eslint-disable-next-line no-console
	console.log( 'License key: ' + licenseKey );
	progressPlannerAjaxRequest( {
		url: progressPlanner.ajaxUrl,
		data: {
			action: 'progress_planner_save_onboard_data',
			_ajax_nonce: progressPlanner.nonce,
			key: licenseKey,
		},
	} );
};

/**
 * Make the AJAX request.
 *
 * @param {Object} data The data to send with the request.
 */
const progressPlannerAjaxAPIRequest = ( data ) => {
	progressPlannerAjaxRequest( {
		url: progressPlanner.onboardAPIUrl,
		data,
		successAction: ( response ) => {
			// Show success message.
			document.getElementById(
				'prpl-account-created-message'
			).style.display = 'block';

			// Hide the form.
			document.getElementById( 'prpl-onboarding-form' ).style.display =
				'none';

			// Make a local request to save the response data.
			progressPlannerSaveLicenseKey( response.license_key );

			// Start scanning posts.
			progressPlannerTriggerScan();
		},
		failAction: ( response ) => {
			// eslint-disable-next-line no-console
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
		data,
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
	document
		.querySelectorAll( 'input[name="with-email"]' )
		.forEach( ( input ) => {
			input.addEventListener( 'change', function () {
				if ( 'no' === this.value ) {
					document
						.getElementById( 'prpl-onboarding-form' )
						.querySelectorAll( 'input' )
						.forEach( ( inputField ) => {
							inputField.required = false;
						} );
					document.querySelector(
						'#prpl-onboarding-form .prpl-form-fields'
					).style.display = 'none';
					document.querySelector(
						'#prpl-onboarding-form .prpl-button-primary'
					).style.display = 'none';
					document.querySelector(
						'#prpl-onboarding-form .prpl-button-secondary--no-email'
					).style.display = 'block';
				} else {
					document
						.getElementById( 'prpl-onboarding-form' )
						.querySelectorAll( 'input' )
						.forEach( ( inputField ) => {
							if (
								'name' === inputField.name ||
								'email' === inputField.name
							) {
								inputField.required = true;
							}
						} );
					document.querySelector(
						'#prpl-onboarding-form .prpl-form-fields'
					).style.display = 'block';
					document.querySelector(
						'#prpl-onboarding-form .prpl-button-primary'
					).style.display = 'block';
					document.querySelector(
						'#prpl-onboarding-form .prpl-button-secondary--no-email'
					).style.display = 'none';
				}
			} );
		} );

	document
		.getElementById( 'prpl-onboarding-form' )
		.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			document.querySelector(
				'#prpl-onboarding-form input[type="submit"]'
			).disabled = true;

			// Figure out whether the user chose to register or not.
			const withEmail = document.querySelector(
				'input[name="with-email"]:checked'
			).value;
			if ( 'no' === withEmail ) {
				// Save a value in the license field.
				progressPlannerSaveLicenseKey( 'no-license' );
				// Start scanning posts.
				progressPlannerTriggerScan();
				return;
			}

			const inputs = this.querySelectorAll( 'input' );

			// Build the data object.
			const data = {};
			inputs.forEach( ( input ) => {
				if ( input.name ) {
					data[ input.name ] = input.value;
				}
			} );
			progressPlannerOnboardCall( data );
		} );
}
