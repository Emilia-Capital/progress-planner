/**
 * Loaded on edit-tags admin pages, this file contains the JavaScript for the ProgressPlanner plugin.
 */

/* global progressPlanner */

/**
 * A helper to make AJAX requests.
 *
 * @param {Object}   params               The callback parameters.
 * @param {string}   params.url           The URL to send the request to.
 * @param {Object}   params.data          The data to send with the request.
 * @param {Function} params.successAction The callback to run on success.
 * @param {Function} params.failAction    The callback to run on failure.
 */
const progressPlannerAjaxRequest = ( { url, data, successAction, failAction } ) => {
	const http = new XMLHttpRequest();
	http.open( 'POST', url, true );
	http.onreadystatechange = () => {
		let response;
		try {
			response = JSON.parse( http.response );
		} catch ( e ) {
			if ( http.readyState === 4 && http.status !== 200 ) {
				// eslint-disable-next-line no-console
				console.warn( http, e );
				return http.response;
			}
		}
		if ( http.readyState === 4 && http.status === 200 ) {
			return successAction ? successAction( response ) : response;
		}
		return failAction ? failAction( response ) : response;
	};

	const dataForm = new FormData();

	// eslint-disable-next-line prefer-const
	for ( let [ key, value ] of Object.entries( data ) ) {
		dataForm.append( key, value );
	}

	http.send( dataForm );
};

const progressPlannerTriggerScan = () => {
	document.getElementById( 'progress-planner-scan-progress' ).style.display = 'block';

	/**
	 * The action to run on a successful AJAX request.
	 * This function should update the UI and re-trigger the scan if necessary.
	 *
	 * @param {Object} response The response from the server.
	 *                          The response should contain a `progress` property.
	 */
	const successAction = ( response ) => {
		const progressBar = document.querySelector( '#progress-planner-scan-progress progress' );
		// Update the progressbar.
		if ( response.data.progress > progressBar.value ) {
			progressBar.value = response.data.progress;
		}

		console.info( `Progress: ${response.data.progress}%, (${response.data.lastScanned}/${response.data.lastPage})` );

		// Refresh the page when scan has finished.
		if ( response.data.progress >= 100 ) {
			location.reload();
			return;
		}

		progressPlannerTriggerScan();
	};

	/**
	 * The AJAX request to run.
	 */
	progressPlannerAjaxRequest( {
		url: progressPlanner.ajaxUrl,
		data: {
			action: 'progress_planner_scan_posts',
			_ajax_nonce: progressPlanner.nonce,
		},
		successAction: successAction,
	} );
};

/**
 * Similar to jQuery's $( document ).ready().
 * Runs a callback when the DOM is ready.
 *
 * @param {Function} callback The callback to run when the DOM is ready.
 */
function progressPlannerDomReady( callback ) {
	if ( document.readyState !== 'loading' ) {
		callback();
		return;
	}
	document.addEventListener( 'DOMContentLoaded', callback );
}

progressPlannerDomReady( () => {
	const scanForm = document.getElementById( 'progress-planner-scan' );
	const resetForm = document.getElementById( 'progress-planner-stats-reset' );

	/**
	 * Add an event listener for the scan form.
	 */
	if ( scanForm ) {
		scanForm.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			scanForm.querySelector( 'input[type="submit"]' ).disabled = true;
			progressPlannerTriggerScan();
		} );
	}

	/**
	 * Add an event listener for the reset form.
	 */
	if ( resetForm ) {
		resetForm.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			resetForm.querySelector( 'input[type="submit"]' ).disabled = true;
			resetForm.querySelector( 'input[type="submit"]' ).value = progressPlanner.l10n.resettingStats;

			// Make an AJAX request to reset the stats.
			progressPlannerAjaxRequest( {
				url: progressPlanner.ajaxUrl,
				data: {
					action: 'progress_planner_reset_stats',
					_ajax_nonce: progressPlanner.nonce,
				},
				successAction: ( response ) => {
					// Refresh the page.
					location.reload();
				},
			} );
		} );
	}
} );

document.getElementById( 'prpl-select-range' ).addEventListener( 'change', function() {
	const range = this.value;
	const url = new URL( window.location.href );
	url.searchParams.set( 'range', range );
	window.location.href = url.href;
} );
