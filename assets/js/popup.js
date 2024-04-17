// Open the popup.
window.progressPlannerPopup = ( context ) => {
	document.body.classList.add( 'prpl-popup-open' );
	document.body.classList.add( 'prpl-popup-' + context );

	// Tweak the vertical position of the popup.
	const popup = document.getElementById( 'prpl-popup-' + context );
	const popupHeight = popup.offsetHeight;
	const windowHeight = window.innerHeight;
	const scrollTop = window.scrollY;
	const popupTop = windowHeight - popupHeight + scrollTop;
	document.getElementById( 'prpl-popup-container' ).style.top =
		popupTop + 'px';
};

// Close the popup.
const progressPlannerPopupClose = () => {
	const bodyClasses = document.body.classList;
	bodyClasses.remove( 'prpl-popup-open' );
	bodyClasses.forEach( ( className ) => {
		if ( className.startsWith( 'prpl-popup-' ) ) {
			bodyClasses.remove( className );
		}
	} );
};

// Close the popup when clicking on the [x] button.
document
	.getElementById( 'prpl-popup-close' )
	.addEventListener( 'click', progressPlannerPopupClose );

// Close the popup when clicking outside it.
document
	.getElementById( 'prpl-popup-body-overlay' )
	.addEventListener( 'click', progressPlannerPopupClose );
