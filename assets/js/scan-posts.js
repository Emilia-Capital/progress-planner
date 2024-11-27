/* global prpl, prplAjaxRequest */

// eslint-disable-next-line no-unused-vars
const prplTriggerScan = () => {
	document.getElementById( 'progress-planner-scan-progress' ).style.display =
		'block';

	/**
	 * The action to run on a successful AJAX request.
	 * This function should update the UI and re-trigger the scan if necessary.
	 *
	 * @param {Object} response The response from the server.
	 *                          The response should contain a `progress` property.
	 */
	const successAction = ( response ) => {
		const progressBar = document.querySelector(
			'#progress-planner-scan-progress progress'
		);
		// Update the progressbar.
		if ( response.data.progress > progressBar.value ) {
			progressBar.value = response.data.progress;
		}

		// eslint-disable-next-line no-console
		console.info(
			`Progress: ${ response.data.progress }%, (${ response.data.lastScanned }/${ response.data.lastPage })`
		);

		// Refresh the page when scan has finished.
		if ( response.data.progress >= 100 ) {
			document.getElementById(
				'progress-planner-scan-progress'
			).style.display = 'none';

			window.location.href =
				window.location.href
					.replace( '&content-scan-finished=true', '' )
					.replace( '&content-scan', '' ) +
				'&content-scan-finished=true';

			return;
		}

		prplTriggerScan();
	};

	const failAction = ( response ) => {
		// If the window.prplFailScanCount is not defined, set it to 1.
		if ( ! window.prplFailScanCount ) {
			window.prplFailScanCount = 1;
		} else {
			window.prplFailScanCount++;
		}

		// If the scan has failed more than 10 times, stop retrying.
		if ( window.prplFailScanCount > 10 ) {
			return;
		}

		console.warn( 'Failed to scan posts. Retrying...' ); // eslint-disable-line no-console
		console.log( response ); // eslint-disable-line no-console
		// Retry after 200ms.
		setTimeout( prplTriggerScan, 200 );
	};

	/**
	 * The AJAX request to run.
	 */
	prplAjaxRequest( {
		url: prpl.ajaxUrl,
		data: {
			action: 'prpl_scan_posts',
			_ajax_nonce: prpl.nonce,
		},
		successAction,
		failAction,
	} );
};

if ( document.getElementById( 'prpl-scan-button' ) ) {
	document
		.getElementById( 'prpl-scan-button' )
		.addEventListener( 'click', ( event ) => {
			event.preventDefault();
			document.getElementById( 'prpl-scan-button' ).disabled = true;
			prplAjaxRequest( {
				url: prpl.ajaxUrl,
				data: {
					action: 'prpl_reset_posts_data',
					_ajax_nonce: prpl.nonce,
				},
				successAction: prplTriggerScan,
				failAction: prplTriggerScan,
			} );
		} );
}
