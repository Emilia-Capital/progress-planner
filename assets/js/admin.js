document
	.getElementById( 'prpl-select-range' )
	.addEventListener( 'change', function () {
		const range = this.value;
		const url = new URL( window.location.href );
		url.searchParams.set( 'range', range );
		window.location.href = url.href;
	} );
document
	.getElementById( 'prpl-select-frequency' )
	.addEventListener( 'change', function () {
		const frequency = this.value;
		const url = new URL( window.location.href );
		url.searchParams.set( 'frequency', frequency );
		window.location.href = url.href;
	} );
