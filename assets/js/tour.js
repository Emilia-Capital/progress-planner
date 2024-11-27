/* global prplTour */
const prplDriver = window.driver.js.driver;

const prplDriverObj = prplDriver( {
	showProgress: true,
	popoverClass: 'prpl-driverjs-theme',
	progressText: prplTour.progressText,
	nextBtnText: prplTour.nextBtnText,
	prevBtnText: prplTour.prevBtnText,
	doneBtnText: prplTour.doneBtnText,
	steps: prplTour.steps,
	onDestroyStarted: () => {
		if ( ! prplDriverObj.hasNextStep() ) {
			const scanFinishedNotice = document.getElementById(
				'prpl-content-scan-finished-notice'
			);
			if ( scanFinishedNotice ) {
				scanFinishedNotice.remove();
			}
		}
		prplDriverObj.destroy();
	},
} );

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
	const prplTourSteps = prplTour.steps;
	prplTourSteps[ 3 ].popover.onNextClick = function () {
		settingsPopover.showPopover();
		prplMakePopoverBackdropTransparent( settingsPopover );
		prplDriverObj.moveNext();
	};
	prplTourSteps[ 4 ].popover.onNextClick = function () {
		settingsPopover.hidePopover();
		document
			.getElementById( settingsPopover.id + '-backdrop-transparency' )
			.remove();
		prplDriverObj.moveNext();
	};

	prplTourSteps[ 6 ].popover.onNextClick = function () {
		monthlyBadgesPopover.showPopover();
		prplMakePopoverBackdropTransparent( monthlyBadgesPopover );
		prplDriverObj.moveNext();
	};
	prplTourSteps[ 7 ].popover.onNextClick = function () {
		monthlyBadgesPopover.hidePopover();
		document
			.getElementById(
				monthlyBadgesPopover.id + '-backdrop-transparency'
			)
			.remove();
		prplDriverObj.moveNext();
	};
	prplDriverObj.drive();

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
