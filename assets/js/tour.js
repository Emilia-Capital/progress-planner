/* global progressPlannerTour */
const driver = window.driver.js.driver;

const driverObj = driver( {
	showProgress: true,
	popoverClass: 'driverjs-theme',
	steps: progressPlannerTour.steps,
} );

document
	.getElementById( 'progress-planner-toggle-tour' )
	.addEventListener( 'click', function () {
		driverObj.drive();
	} );
