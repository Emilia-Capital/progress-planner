/* global customElements, HTMLElement */

/**
 * Register the custom web component.
 */
customElements.define(
	'prpl-chart-bar',
	class extends HTMLElement {
		constructor( data = [] ) {
			// Get parent class properties
			super();

			if ( data.length === 0 ) {
				data = JSON.parse( this.getAttribute( 'data' ) );
			}

			const labelsDivider =
				data.length > 6 ? parseInt( data.length / 6 ) : 1;

			let html = `<div class="chart-bar" style="display: flex; max-width: 600px; height: 200px; width: 100%; align-items: flex-end; gap: 5px; margin: 1rem 0;">`;
			let i = 0;
			data.forEach( ( item ) => {
				html += `<div style="flex: auto; display: flex; flex-direction: column; justify-content: flex-end; height: 100%;">`;
				html += `<div style="
					display: block;
					width: 100%;
					height: ${ item.score }%;
					background: ${ item.color }"
					title="${ item.label } - ${ item.score }%
				"></div>`;
				// Only display up to 6 labels.
				html += `<span class="label-container" style="height:1rem;overflow:visible;text-align:center;display:block;width:100%;font-size: 0.75em;">`;
				html +=
					i % labelsDivider === 0
						? `<span class="label visible">${ item.label }</span>`
						: `<span class="label invisible" style="visibility: hidden;">${ item.label }</span>`;
				html += `</span>`;
				html += `</div>`;
				i++;
			} );
			html += `</div>`;

			this.innerHTML = html;

			// Tweak labels styling to fix positioning when there are many items.
			if ( this.querySelectorAll( '.label.invisible' ).length > 0 ) {
				this.querySelectorAll( '.label-container' ).forEach(
					( label ) => {
						const labelWidth =
							label.querySelector( '.label' ).offsetWidth;
						const labelElement = label.querySelector( '.label' );
						labelElement.style.display = 'block';
						labelElement.style.width = 0;
						const marginLeft =
							( label.offsetWidth - labelWidth ) / 2;
						if ( labelElement.classList.contains( 'visible' ) ) {
							labelElement.style.marginLeft = `${ marginLeft }px`;
						}
					}
				);
				// Reduce the gap between items to avoid overflows.
				this.querySelector( '.chart-bar' ).style.gap =
					parseInt(
						Math.max(
							this.querySelector( '.label' ).offsetWidth / 4,
							1
						)
					) + 'px';
			}
		}
	}
);
