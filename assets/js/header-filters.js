const driver = window.driver.js.driver;

const driverObj = driver( {
	showProgress: true,
	popoverClass: 'driverjs-theme',
	steps: [
		{
			element: '.prpl-website-activity-score',
			popover: {
				title: 'Website activity score',
				description: "This is the website activity score. It shows how active you've been on your website.",
				side: 'top',
				align: 'center',
			},
		},
		{
			element: '.prpl-activity-scores',
			popover: {
				title: 'Longterm activity score',
				description: "Here, we show you your longterm activity score. This shows whether you've been active on your website over a longer period of time.",
				side: 'top',
				align: 'center',
			},
		},
		{
			element: '.prpl-todo',
			popover: {
				title: 'Your to-do list',
				description: 'This is where you can see your to-do list. You can add tasks to your to-do list by clicking the "Add to do" button. You can also see these to-do items on your dashboard.',
				side: 'top',
				align: 'center',
			},
		},
		{
			element: '.prpl-badges-progress',
			popover: {
				title: 'Your badges',
				description: 'As you progress and are more active on your website, you can earn badges. These badges are displayed here!',
				side: 'top',
				align: 'center',
			},
		},
		{
			element: '.prpl-badges-progress .prpl-info-icon',
			popover: {
				title: 'Your badge progress',
				description: 'Clicking the info icon will show you more information about your badge progress.',
				side: 'top',
				align: 'center',
			},
		},
		{
			element: '.prpl-latest-badge',
			popover: {
				title: 'Your latest badge',
				description: 'This is your latest badge. Click it to share it with your friends!',
				side: 'top',
				align: 'center',
			},
		},
	],
} );

document
	.getElementById( 'progress-planner-toggle-tour' )
	.addEventListener( 'click', function () {
		driverObj.drive();
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
