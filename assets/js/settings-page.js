/* global progressPlannerSettingsPage, alert */

/**
 * Vanilla JS version of jQuery( document ).ready().
 *
 * @param {Function} fn The function to run when the document is ready.
 */
const prplDocumentReady = function ( fn ) {
	if ( document.readyState !== 'loading' ) {
		fn();
		return;
	}
	document.addEventListener( 'DOMContentLoaded', fn );
};

/**
 * Toggle the visibility of the edit action,
 * depending on whether a page is selected.
 *
 * @param {string} page The page.
 */
const prplToggleEditActionVisibility = function ( page ) {
	const itemActionsEl = document.querySelector(
		`.prpl-pages-item[data-page-item="${ page }"] .item-actions`
	);
	if ( ! itemActionsEl ) {
		return;
	}
	const selectEl = itemActionsEl.querySelector(
		'[data-action="select"] select'
	);
	const editEl = itemActionsEl.querySelector( '[data-action="edit"]' );
	const value = selectEl.value;
	if ( ! value || value.length === 0 ) {
		editEl.style.display = 'none';
	} else {
		editEl.style.display = 'block';
		editEl.querySelector(
			'a'
		).href = `${ progressPlannerSettingsPage.siteUrl }/wp-admin/post.php?post=${ value }&action=edit`;
	}
};

/**
 * Handle showing/hiding the edit action,
 * based on whether a page is selected.
 * Also changes the link of the edit action.
 */
prplDocumentReady( function () {
	document.querySelectorAll( 'select' ).forEach( function ( select ) {
		const page = select
			.closest( '.prpl-pages-item' )
			.getAttribute( 'data-page-item' );

		prplToggleEditActionVisibility( page );
		if ( select ) {
			select.addEventListener( 'change', function () {
				prplToggleEditActionVisibility( page );
			} );
		}
	} );
} );

const prplTogglePageSelectorSettingVisibility = function ( page, value ) {
	const pageSelectorWrapperEl = document.querySelector(
		`.prpl-pages-item-${ page } .item-actions`
	);

	if ( ! pageSelectorWrapperEl ) {
		return;
	}

	// Hide entire page selector setting if needed.
	if ( 'not-applicable' === value ) {
		pageSelectorWrapperEl.style.display = 'none';
		pageSelectorWrapperEl.querySelector( 'select' ).value = '';
		pageSelectorWrapperEl.querySelector(
			'[data-action="edit"]'
		).style.display = 'none';
	}

	// Show only create button.
	if ( 'no' === value ) {
		pageSelectorWrapperEl.style.display = 'flex';
		pageSelectorWrapperEl.querySelector( 'select' ).value = '';
		pageSelectorWrapperEl.querySelector(
			'[data-action="edit"]'
		).style.display = 'none';
		pageSelectorWrapperEl.querySelector(
			'.prpl-select-page'
		).style.display = 'none';
		pageSelectorWrapperEl.querySelector(
			'[data-action="create"]'
		).style.display = 'block';
	}

	// Show only select and edit button.
	if ( 'yes' === value ) {
		pageSelectorWrapperEl.style.display = 'flex';
		pageSelectorWrapperEl.querySelector(
			'.prpl-select-page'
		).style.display = 'flex';
		pageSelectorWrapperEl.querySelector(
			'[data-action="create"]'
		).style.display = 'none';
	}
};

prplDocumentReady( function () {
	document
		.querySelectorAll( 'input[type="radio"][data-page]' )
		.forEach( function ( radio ) {
			const page = radio.getAttribute( 'data-page' ),
				value = radio.value;

			if ( radio ) {
				// Show/hide the page selector setting if radio is checked.
				if ( radio.checked ) {
					prplTogglePageSelectorSettingVisibility( page, value );
				}

				// Add listeners for all radio buttons.
				radio.addEventListener( 'change', function () {
					prplTogglePageSelectorSettingVisibility( page, value );
				} );
			}
		} );
} );

/**
 * Handle the form submission.
 */
prplDocumentReady( function () {
	document
		.getElementById( 'prpl-settings-submit' )
		.addEventListener( 'click', function () {
			const formData = new FormData(
				document.getElementById( 'prpl-settings' )
			);
			const data = {
				action: 'prpl_settings_form',
			};
			formData.forEach( function ( value, key ) {
				data[ key ] = value;
			} );
			const request = wp.ajax.post( 'prpl_settings_form', data );
			request.done( function () {
				window.location.reload();
			} );
			request.fail( function ( response ) {
				alert( response.licensingError || response ); // eslint-disable-line no-alert
			} );
		} );
} );
