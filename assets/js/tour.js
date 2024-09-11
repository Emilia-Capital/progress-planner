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
} );

// eslint-disable-next-line no-unused-vars -- This is called on a few buttons.
function prplStartTour() {
	driverObj.drive();
}
