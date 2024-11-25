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

			let html = `<div style="
				display: grid;
				grid-template-columns: repeat(${ data.length }, 1fr);
				height: 200px;
				width: 100%;
				max-width: 100%;
				gap: 5px;
				margin: 1rem 0;
			">`;
			let i = 0;
			data.forEach( ( item ) => {
				html += `<div style="display: flex; flex-direction: column; justify-content: flex-end; height: 100%;">`;
				html += `<div style="
					display: block;
					width: 100%;
					height: ${ item.score }%;
					background: ${ item.color }"
					title="${ item.label } - ${ item.score }%
				"></div>`;
				// Only display up to 6 labels.
				html += `<span class="prpl-chart-bar-label" style="height:1rem;overflow:visible;text-align:center;font-size: 0.75em;">`;
				if ( i % labelsDivider === 0 ) {
					html += `<span class="visible">${ item.label }</span>`;
				} else {
					html += `<span style="visibility: hidden;">${ item.label }</span>`;
				}
				html += `</span>`;
				html += `</div>`;
				i++;
			} );
			html += `</div>`;

			this.innerHTML = html;

			// Center labels.
			// This is needed in order to avoid uneven spacing
			// when there are many items in the chart.
			this.querySelectorAll( '.prpl-chart-bar-label .visible' ).forEach(
				( label ) => {
					const marginLeft =
						( label.parentElement.offsetWidth -
							label.offsetWidth ) /
						2;
					label.style.width = 0;
					label.style.display = 'block';
					if ( 0 <= marginLeft ) {
						label.style.marginLeft = `${ marginLeft }px`;
					}
				}
			);
		}
	}
);
