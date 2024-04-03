/* global progressPlanner */


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
			document.getElementById( 'progress-planner-scan-progress' ).style.display = 'none';
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
