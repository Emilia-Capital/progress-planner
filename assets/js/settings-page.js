/* global progressPlannerSettingsPage, alert */

/**
 * Vanilla JS version of jQuery( document ).ready().
 *
 * @param {Function} fn The function to run when the document is ready.
 */
const prplProDocumentReady = function ( fn ) {
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
const prplProTogglePopover = function ( triggerEl, action ) {
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

const prplProToggleRequiredPagesVisibility = function ( siteType ) {
	// Get an array of available pages.
	const availablePages = [];
	document
		.querySelectorAll( '.prpl-pro-pages-item' )
		.forEach( function ( el ) {
			availablePages.push( el.getAttribute( 'data-page-item' ) );
		} );

	// Get the required pages for the site type.
	const requiredPages = [];
	progressPlannerSettingsPage.siteTypes.forEach( function ( siteTypeItem ) {
		if ( siteTypeItem.id === siteType ) {
			requiredPages.push( ...siteTypeItem.pages );
		}
	} );

	// Hide pages not required for this site-type.
	availablePages.forEach( function ( page ) {
		const el = document.querySelector( `.prpl-pro-pages-item-${ page }` );
		el.style.display = requiredPages.includes( page ) ? 'block' : 'none';
	} );
};

/**
 * Toggle the visibility of page context actions.
 *
 * @param {string} page    The page.
 * @param {string} hasPage Whether the page has the page or not.
 */
const prplProTogglePageContextVisibility = function ( page, hasPage ) {
	const itemActionsEl = document.querySelector(
		`.item-actions[data-page="${ page }"]`
	);
	if ( ! itemActionsEl ) {
		return;
	}
	const actions = {
		select: [ 'none', 'block' ],
		create: [ 'block', 'none' ],
		edit: [ 'none', 'block' ],
		plan: [ 'block', 'block' ],
		assign: [ 'block', 'block' ],
	};
	Object.keys( actions ).forEach( function ( key ) {
		itemActionsEl.querySelector(
			`[data-action="${ key }"]`
		).style.display =
			hasPage === 'no' ? actions[ key ][ 1 ] : actions[ key ][ 0 ];
	} );
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
prplProDocumentReady( function () {
	const selectors = [
		'.page-selector-button',
		'.page-plan-button',
		'.page-assign-button',
	];
	// Open the popovers.
	selectors.forEach( function ( selector ) {
		document.querySelectorAll( selector ).forEach( function ( el ) {
			el.addEventListener( 'click', function () {
				prplProTogglePopover( el, 'show' );
			} );
		} );
	} );

	// Close the popovers.
	selectors.forEach( function ( selector ) {
		document
			.querySelectorAll( `${ selector }-close` )
			.forEach( function ( el ) {
				el.addEventListener( 'click', function () {
					prplProTogglePopover( el, 'hide' );
				} );
			} );
	} );
} );

/**
 * Handle the form submission.
 */
prplProDocumentReady( function () {
	document
		.getElementById( 'prpl-settings-submit' )
		.addEventListener( 'click', function () {
			const formData = new FormData(
				document.getElementById( 'prpl-pro-settings' )
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
 * Handle showing/hiding page controls,
 * based on whether they have the page or not.
 */
prplProDocumentReady( function () {
	document
		.querySelectorAll( 'input[type="radio"]' )
		.forEach( function ( radio ) {
			if ( radio.hasAttribute( 'data-page' ) ) {
				// Check if the radio is checked.
				if ( radio.checked ) {
					prplProTogglePageContextVisibility(
						radio.getAttribute( 'data-page' ),
						radio.value
					);
				}
			}
			radio.addEventListener( 'change', function () {
				prplProTogglePageContextVisibility(
					radio.getAttribute( 'data-page' ),
					radio.value
				);
			} );
		} );
} );

/**
 * Handle showing/hiding the edit action,
 * based on whether a page is selected.
 * Also changes the link of the edit action.
 */
prplProDocumentReady( function () {
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

/**
 * Handle toggling the page selectors based on what type of site this is.
 */
prplProDocumentReady( function () {
	// Get the site type.
	const selector = document.getElementById( 'prpl-setting-site_type' );
	if ( ! selector ) {
		return;
	}
	prplProToggleRequiredPagesVisibility( selector.value );

	// Listen for changes to the site type.
	selector.addEventListener( 'change', function () {
		prplProToggleRequiredPagesVisibility( selector.value );
	} );
} );
