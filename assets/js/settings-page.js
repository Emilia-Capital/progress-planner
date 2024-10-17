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
 * Open a popover from a trigger element.
 *
 * @param {Element} triggerEl The trigger element.
 * @param {string}  action    The action to perform.
 */
const prplTogglePopover = function ( triggerEl, action ) {
	// Get the popover.
	const popover = document.getElementById(
		triggerEl.getAttribute( 'popovertarget' )
	);
	if ( ! popover ) {
		return;
	}
	switch ( action ) {
		case 'show':
			popover.showPopover();
			break;

		case 'hide':
			popover.hidePopover();
			break;

		default:
			popover.togglePopover();
			break;
	}
};

/**
 * Toggle the visibility of the edit action,
 * depending on whether a page is selected.
 *
 * @param {string} page The page.
 */
const prplProToggleEditActionVisibility = function ( page ) {
	const itemActionsEl = document.querySelector(
		`.item-actions[data-page="${ page }"]`
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
 * Toggle popovers.
 */
prplDocumentReady( function () {
	const selectors = [
		'.page-selector-button',
		'.page-plan-button',
		'.page-assign-button',
	];
	// Open the popovers.
	selectors.forEach( function ( selector ) {
		document.querySelectorAll( selector ).forEach( function ( el ) {
			el.addEventListener( 'click', function () {
				prplTogglePopover( el, 'show' );
			} );
		} );
	} );

	// Close the popovers.
	selectors.forEach( function ( selector ) {
		document
			.querySelectorAll( `${ selector }-close` )
			.forEach( function ( el ) {
				el.addEventListener( 'click', function () {
					prplTogglePopover( el, 'hide' );
				} );
			} );
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

/**
 * Handle showing/hiding the edit action,
 * based on whether a page is selected.
 * Also changes the link of the edit action.
 */
prplDocumentReady( function () {
	document.querySelectorAll( 'select' ).forEach( function ( select ) {
		const page =
			select.parentElement.parentElement.getAttribute( 'data-page' );

		prplProToggleEditActionVisibility( page );
		if ( select ) {
			select.addEventListener( 'change', function () {
				prplProToggleEditActionVisibility( page );
			} );
		}
	} );
} );
