document.getElementById( 'prpl-select-range' ).addEventListener( 'change', function() {
	const range = this.value;
	const url = new URL( window.location.href );
	url.searchParams.set( 'range', range );
	window.location.href = url.href;
} );
document.getElementById( 'prpl-select-frequency' ).addEventListener( 'change', function() {
	const frequency = this.value;
	const url = new URL( window.location.href );
	url.searchParams.set( 'frequency', frequency );
	window.location.href = url.href;
} );

window.progressPlannerPopup = ( context ) => {
	document.body.classList.add( 'prpl-popup-open' );
	document.body.classList.add( 'prpl-popup-' + context );

	// Tweak the vertical position of the popup.
	const popup = document.getElementById( 'prpl-popup-' + context );
	const popupHeight = popup.offsetHeight;
	const windowHeight = window.innerHeight;
	const scrollTop = window.scrollY;
	const popupTop = windowHeight - popupHeight + scrollTop;
	document.getElementById( 'prpl-popup-container' ).style.top = popupTop + 'px';

};

document.getElementById( 'prpl-popup-body-overlay' ).addEventListener( 'click', function() {
	const bodyClasses = document.body.classList;
	bodyClasses.remove( 'prpl-popup-open' );
	bodyClasses.forEach( ( className ) => {
		if ( className.startsWith( 'prpl-popup-' ) ) {
			bodyClasses.remove( className );
		}
	} );
} );
