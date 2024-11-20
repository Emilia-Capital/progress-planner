/* global customElements, HTMLElement */

/**
 * Gauge.
 */
customElements.define(
	'prpl-badge',
	class extends HTMLElement {
		constructor( badgeId ) {
			// Get parent class properties
			super();

			this.innerHTML = `
				<img
					src="https://progressplanner.com/wp-json/progress-planner-saas/v1/badge-svg/?badge_id=${
						badgeId || this.getAttribute( 'badge-id' )
					}"
					alt="Badge"
				/>
			`;
		}
	}
);
