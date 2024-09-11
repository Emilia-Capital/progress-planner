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

document
	.getElementById( 'progress-planner-toggle-tour' )
	.addEventListener( 'click', function () {
		driverObj.drive();
	} );

document
	.getElementById( 'prpl-start-tour-button' )
	.addEventListener( 'click', function () {
		driverObj.drive();
	} );

document
	.getElementById( 'prpl-start-tour-icon-button' )
	.addEventListener( 'click', function () {
		driverObj.drive();
	} );
