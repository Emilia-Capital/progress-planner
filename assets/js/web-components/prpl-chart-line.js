/* global customElements, HTMLElement */

/**
 * Gauge.
 */
customElements.define(
	'prpl-chart-line',
	class extends HTMLElement {
		constructor( data = [] ) {
			// Get parent class properties
			super();

			// If data is empty, get the data from the contents.
			if ( 0 === data.length ) {
				data = JSON.parse( this.getAttribute( 'data' ) );
			}

			const aspectRatio = 2;
			const height = 300;
			const axisOffset = 16;
			const width = height * aspectRatio;
			const strokeWidth = 4;

			// Determine the maximum value for the chart.
			const max = Math.max( ...data.data );
			const maxValue = 100 > max && 70 < max ? 100 : max;

			const calcYCoordinate = ( value ) => {
				const multiplier = ( height - axisOffset * 2 ) / height;
				const yCoordinate =
					( maxValue - value * multiplier ) * ( height / maxValue ) -
					axisOffset;
				return yCoordinate - strokeWidth / 2;
			};

			// Calculate the Y axis labels.
			// Take the maximum value and divide it by 4 to get the step.
			const yLabelsStep = maxValue / 4;

			// Calculate the Y axis labels.
			const yLabels = [];
			if ( 100 === maxValue || 15 > maxValue ) {
				for ( let i = 0; i <= 4; i++ ) {
					yLabels.push( parseInt( yLabelsStep * i ) );
				}
			} else {
				// Round the values to the nearest 10.
				for ( let i = 0; i <= 4; i++ ) {
					yLabels.push(
						Math.min( maxValue, Math.round( yLabelsStep * i, -1 ) )
					);
				}
			}

			// Calculate the distance between the points in the X axis.
			const xDistanceBetweenPoints = Math.round(
				( width - 2 * axisOffset ) / ( data.data.length - 1 )
			);

			// X-axis line.
			const xAxisLine = `<g><line x1="${ axisOffset * 2 }" x2="${
				aspectRatio * height
			}" y1="${ height - axisOffset }" y2="${
				height - axisOffset
			}" stroke="var(--prpl-color-gray-2)" stroke-width="1" /></g>`;

			// Y-axis line.
			const yAxisLine = `<g><line x1="${ axisOffset * 2 }" x2="${
				axisOffset * 2
			}" y1="${ axisOffset }" y2="${
				height - axisOffset
			}" stroke="var(--prpl-color-gray-2)" stroke-width="1" /></g>`;

			// X-axis labels and rulers.
			let labelXCoordinate = 0;
			const labelsXCount = data.labels.length;
			const labelsXDivider = Math.round( labelsXCount / 6 );
			let i = 0;
			let xAxisLabelsAndRulers = '';
			for ( const label of data.labels ) {
				labelXCoordinate = xDistanceBetweenPoints * i + axisOffset;
				++i;

				// Only allow up to 6 labels to prevent overlapping.
				// If there are more than 6 labels, find the alternate labels.
				if (
					6 < labelsXCount &&
					1 !== i &&
					( i - 1 ) % labelsXDivider !== 0
				) {
					continue;
				}

				xAxisLabelsAndRulers += `<g><text class="x-axis-label" x="${ labelXCoordinate }" y="${
					height + axisOffset
				}">${ label }</text></g>`;

				// Draw the ruler.
				if ( 1 !== i ) {
					xAxisLabelsAndRulers += `<g><line x1="${
						labelXCoordinate + axisOffset
					}" x2="${
						labelXCoordinate + axisOffset
					}" y1="${ axisOffset }" y2="${
						height - axisOffset
					}" stroke="var(--prpl-color-gray-1)" stroke-width="1" /></g>`;
				}
			}

			// Y-axis labels and rulers.
			let yLabelCoordinate = 0;
			let iYLabel = 0;
			let yAxisLabelsAndRulers = '';
			for ( const yLabel of yLabels ) {
				yLabelCoordinate = calcYCoordinate( yLabel );

				yAxisLabelsAndRulers += `<g><text class="y-axis-label" x="0" y="${
					yLabelCoordinate + axisOffset / 2
				}">${ yLabel }</text></g>`;

				// Draw the ruler.
				if ( 0 !== iYLabel ) {
					yAxisLabelsAndRulers += `<g><line x1="${
						axisOffset * 2
					}" x2="${
						aspectRatio * height
					}" y1="${ yLabelCoordinate }" y2="${ yLabelCoordinate }" stroke="var(--prpl-color-gray-2)" stroke-width="1" /></g>`;
				}

				++iYLabel;
			}

			// Line chart.
			const polylinePoints = [];
			let xCoordinate = axisOffset * 2;
			for ( const point of data.data ) {
				polylinePoints.push( [
					xCoordinate,
					calcYCoordinate( point ),
				] );
				xCoordinate += xDistanceBetweenPoints;
			}

			const polyLine = `<g><polyline fill="none" stroke="${
				data.color[ 0 ]
			}" stroke-width="${ strokeWidth }" points="${ polylinePoints
				.map( ( point ) => point.join( ',' ) )
				.join( ' ' ) }" /></g>`;

			this.innerHTML = `<svg viewBox="0 0 ${ parseInt(
				height * aspectRatio + axisOffset * 2
			) } ${ parseInt( height + axisOffset * 2 ) }">
				${ xAxisLine }
				${ yAxisLine }
				${ xAxisLabelsAndRulers }
				${ yAxisLabelsAndRulers }
				${ polyLine }
			</svg>`;
		}
	}
);
