document
	.querySelector( '.prpl-challenge-steps-header' )
	.querySelectorAll( 'button' )
	.forEach( ( button ) => {
		button.addEventListener( 'click', ( event ) => {
			const step = event.target.dataset.step;
			document
				.querySelectorAll( '.prpl-challenge-step' )
				.forEach( ( stepEl ) => {
					stepEl.style.display =
						stepEl.dataset.step === step ? 'block' : 'none';
				} );
		} );
	} );
