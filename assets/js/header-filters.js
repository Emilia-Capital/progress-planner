const driver = window.driver.js.driver;

const driverObj = driver();

driverObj.highlight( {
	element: '#progress-planner-toggle-tour',
	popover: {
		title: 'Click here',
		description: 'This will start the Progress Planner Tour.',
	},
} );

// Handle changes to the range dropdown.
document
	.getElementById( 'prpl-select-range' )
	.addEventListener( 'change', function () {
		const range = this.value;
		const url = new URL( window.location.href );
		url.searchParams.set( 'range', range );
		window.location.href = url.href;
	} );

// Handle changes to the frequency dropdown.
document
	.getElementById( 'prpl-select-frequency' )
	.addEventListener( 'change', function () {
		const frequency = this.value;
		const url = new URL( window.location.href );
		url.searchParams.set( 'frequency', frequency );
		window.location.href = url.href;
	} );
