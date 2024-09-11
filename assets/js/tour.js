/* global progressPlannerTour */
const driver = window.driver.js.driver;

const driverObj = driver( {
	showProgress: true,
	popoverClass: 'driverjs-theme',
	progressText: progressPlannerTour.progressText,
	nextBtnText: progressPlannerTour.nextBtnText,
	prevBtnText: progressPlannerTour.prevBtnText,
	doneBtnText: progressPlannerTour.doneBtnText,
	steps: progressPlannerTour.steps,
	onDestroyStarted: () => {
		if ( ! driverObj.hasNextStep() ) {
			document
				.getElementById( 'prpl-content-scan-finished-notice' )
				.remove();
			driverObj.destroy();
		}
	},
} );

// Function to make the backdrop of a popover transparent.
function makePopoverBackdropTransparent( popover ) {
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
	const progressPlannerTourSteps = progressPlannerTour.steps;
	progressPlannerTourSteps[ 3 ].popover.onNextClick = function () {
		settingsPopover.showPopover();
		makePopoverBackdropTransparent( settingsPopover );
		driverObj.moveNext();
	};
	progressPlannerTourSteps[ 4 ].popover.onNextClick = function () {
		settingsPopover.hidePopover();
		document
			.getElementById( settingsPopover.id + '-backdrop-transparency' )
			.remove();
		driverObj.moveNext();
	};
	driverObj.drive();
}
