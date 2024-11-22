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

			let html = `<div style="display: flex; max-width: 600px; height: 200px; width: 100%; align-items: flex-end; gap: 5px; margin: 1rem 0;">`;
			let i = 0;
			for ( const score of data.datasets[ 0 ].data ) {
				html += `
				<div style="flex: auto; display: flex; flex-direction: column; justify-content: flex-end; height: 100%;">
					<div style="
						display: block;
						width: 100%;
						height: ${ score }%;
						background: ${ data.datasets[ 0 ].backgroundColor[ i ] }"
						title="${ score }%
					"></div>
					<span style="text-align:center;display:block;width:100%;font-size: 0.75em;">${ data.labels[ i ] }</span>
				</div>`;
				i++;
			}
			html += `</div>`;

			this.innerHTML = html;
		}
	}
);
