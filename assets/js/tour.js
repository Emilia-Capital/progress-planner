/* global progressPlannerTour */
const prplDriver = window.driver.js.driver;

const prplDriverObj = prplDriver( {
	showProgress: true,
	popoverClass: 'prpl-driverjs-theme',
	progressText: progressPlannerTour.progressText,
	nextBtnText: progressPlannerTour.nextBtnText,
	prevBtnText: progressPlannerTour.prevBtnText,
	doneBtnText: progressPlannerTour.doneBtnText,
	steps: progressPlannerTour.steps,
	onDestroyStarted: () => {
		if ( ! prplDriverObj.hasNextStep() ) {
			const scanFinishedNotice = document.getElementById(
				'prpl-content-scan-finished-notice'
			);
			if ( scanFinishedNotice ) {
				scanFinishedNotice.remove();
			}
		}

		// Remove tour_step from URL when tour is destroyed.
		const newUrl = new URL( window.location );
		newUrl.searchParams.delete( 'tour_step' );
		window.history.replaceState( {}, '', newUrl );

		prplDriverObj.destroy();
	},
	onPopoverRender: (
		popover, // eslint-disable-line no-unused-vars
		{ config, state } // eslint-disable-line no-unused-vars
	) => {
		const settingsPopover = document.getElementById(
			'prpl-popover-settings'
		);
		const monthlyBadgesPopover = document.getElementById(
			'prpl-popover-monthly-badges'
		);

		if ( state.activeIndex === 4 ) {
			prplTourShowPopover( settingsPopover );
		}

		if ( state.activeIndex === 7 ) {
			prplTourShowPopover( monthlyBadgesPopover );
		}

		// Update URL with current step.
		const newUrl = new URL( window.location );
		newUrl.searchParams.set( 'tour_step', state.activeIndex );
		window.history.replaceState( {}, '', newUrl );
	},
} );

function prplTourShowPopover( popover ) {
	popover.showPopover();
	prplMakePopoverBackdropTransparent( popover );
}

function prplTourHidePopover( popover ) {
	popover.hidePopover();
	document.getElementById( popover.id + '-backdrop-transparency' ).remove();
}

// Function to make the backdrop of a popover transparent.
function prplMakePopoverBackdropTransparent( popover ) {
	if ( popover ) {
		const style = document.createElement( 'style' );
		style.id = popover.id + '-backdrop-transparency';
		style.innerHTML = `
					#${ popover.id }::backdrop {
							background-color: transparent !important;
					}
			`;
		document.head.appendChild( style );
	}
}

// eslint-disable-next-line no-unused-vars -- This is called on a few buttons.
function prplStartTour() {
	const settingsPopover = document.getElementById( 'prpl-popover-settings' );
	const monthlyBadgesPopover = document.getElementById(
		'prpl-popover-monthly-badges'
	);
	const progressPlannerTourSteps = progressPlannerTour.steps;
	progressPlannerTourSteps[ 3 ].popover.onNextClick = function () {
		prplTourShowPopover( settingsPopover );
		prplDriverObj.moveNext();
	};
	progressPlannerTourSteps[ 4 ].popover.onNextClick = function () {
		prplTourHidePopover( settingsPopover );
		prplDriverObj.moveNext();
	};

	progressPlannerTourSteps[ 6 ].popover.onNextClick = function () {
		prplTourShowPopover( monthlyBadgesPopover );
		prplDriverObj.moveNext();
	};
	progressPlannerTourSteps[ 7 ].popover.onNextClick = function () {
		prplTourHidePopover( monthlyBadgesPopover );
		prplDriverObj.moveNext();
	};

	// Check URL parameters.
	const urlParams = new URLSearchParams( window.location.search );
	const savedStepIndex = urlParams.get( 'tour_step' );

	if ( null !== savedStepIndex ) {
		// Start from saved step.
		prplDriverObj.drive( parseInt( savedStepIndex, 10 ) );
	} else {
		// Start from beginning.
		prplDriverObj.drive( 0 );
	}

	// Remove `content-scan-finished=true` from the URL, without refreshing the page.
	window.history.replaceState(
		{},
		document.title,
		window.location.href
			.replace( '&content-scan-finished=true', '' )
			.replace( 'content-scan-finished=true', '' )
	);
}

// Start the tour if the URL contains the query parameter.
if ( window.location.href.includes( 'content-scan-finished=true' ) ) {
	prplStartTour();
}
