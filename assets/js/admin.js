/**
 * Loaded on edit-tags admin pages, this file contains the JavaScript for the ProgressPlanner plugin.
 *
 * @file   This files contains the functionality for the ProgressPlanner plugin.
 * @author Joost de Valk
 */

/* global progressPlanner, tb_remove */

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
	progressPlannerAjaxRequest( {
		url: progressPlanner.ajaxUrl,
		data: {
			action: 'progress_planner_scan_posts',
			_ajax_nonce: progressPlanner.nonce,
		},
		successAction: ( response ) => {
			document.querySelector( '#progress-planner-scan-progress progress' ).value = response.data.progress;

			progressPlannerTriggerScan();
			if ( response.data.progress >= 100 ) {
				location.reload();
			}
		},
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
	if ( scanForm ) {
		scanForm.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			progressPlannerTriggerScan();
		} );
	}
	if ( resetForm ) {
		resetForm.addEventListener( 'submit', ( e ) => {
			e.preventDefault();
			resetForm.querySelector( 'input[type="submit"]' ).disabled = true;
			progressPlannerAjaxRequest( {
				url: progressPlanner.ajaxUrl,
				data: {
					action: 'progress_planner_reset_stats',
					_ajax_nonce: progressPlanner.nonce,
				},
				successAction: ( response ) => {
					resetForm.querySelector( 'input[type="submit"]' ).value = progressPlanner.l10n.resettingStats;
					// Refresh the page.
					location.reload();
				},
			} );
		} );
	}
} );
