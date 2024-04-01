/* global progressPlanner */

if ( document.getElementById( 'prpl-onboarding-form' ) ) {
	document.getElementById( 'prpl-onboarding-form' ).addEventListener( 'submit', function( event ) {
		event.preventDefault();
		const inputs = this.querySelectorAll( 'input' );

		// Build the data object.
		const data   = {};
		inputs.forEach( input => {
			if ( input.name ) {
				data[ input.name ] = input.value;
			}
		} );

		// Make a request to get the nonce.
		// Once the nonce is received, make a request to the API.
		progressPlannerAjaxRequest( {
			method: 'POST',
			url: progressPlanner.onboardGetNonceURL,
			data: data,
			successAction: ( response ) => {
				if ( 'ok' === response.status ) {

					// Add the nonce to our data object.
					data.nonce = response.nonce;
console.log(data);
					// Make the request to the API.
					progressPlannerAjaxRequest( {
						method: 'POST',
						url: progressPlanner.onboardAPIUrl,
						data: data,
						successAction: ( response ) => {
							console.log( data );
							console.log( response );
							if ( response.success ) {
							}
						},
						failAction: ( response ) => {
							console.log( response );
						},
					} );
				}
			},
		} );
	} );
}
