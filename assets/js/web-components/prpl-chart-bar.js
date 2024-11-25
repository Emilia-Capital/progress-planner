/* global customElements, HTMLElement */

/**
 * Gauge.
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

			let html = `<div style="display: flex; max-width: 600px; height: 200px; width: 100%; align-items: flex-end; gap: 5px; margin: 1rem 0;">`;
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
				html += `<span style="width:0!important;height:1rem;overflow:visible;text-align:center;display:block;width:100%;font-size: 0.75em;">`;
				if ( i % labelsDivider === 0 ) {
					html += `${ item.label }`;
				}
				html += `</span>`;
				html += `</div>`;
				i++;
			} );
			html += `</div>`;

			this.innerHTML = html;
		}
	}
);
