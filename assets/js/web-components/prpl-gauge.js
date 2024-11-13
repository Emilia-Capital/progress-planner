/* global customElements, HTMLElement */

/**
 * Big counter.
 */
customElements.define(
	'prpl-gauge',
	class extends HTMLElement {
		constructor() {
			// Get parent class properties
			super();

			const value = this.getAttribute( 'value' );
			const max = this.getAttribute( 'max' );
			const maxDeg = this.getAttribute( 'maxDeg' ) || '180deg';
			const background = this.getAttribute( 'background' );
			const color = this.getAttribute( 'color' ) || 'var(--prpl-color-accent-orange)';
			const start = this.getAttribute( 'start' ) || '-135deg';
			const cutout = this.getAttribute( 'cutout' ) || '57%';
			const contentFontSize = this.getAttribute( 'contentFontSize' ) || 'var(--prpl-font-size-6xl)';
			let content = this.innerHTML;
			if ( this.querySelector( 'img' ) || this.querySelector( 'svg' ) ) {
				content = `
					<span style="top: -1em; display: block; padding-top: 25%; text-align: center; position: absolute;width: 100%;">
						<span style="display:inline-block;width: 50%;">
							${ this.innerHTML }
						</span>
					</span>
				`;
			} else {
				content = `
					<span style="font-size: ${ contentFontSize }; top: -1em; display: block; padding-top: 50%; font-weight: 600; text-align: center; position: absolute; color: var(--prpl-color-gray-5); width: 100%; line-height: 1.2;">
						${ this.innerHTML }
					</span>
				`;
			}

			this.innerHTML = `
			<div style="padding: var(--prpl-padding); background: ${ background }; border-radius:var(--prpl-border-radius); aspect-ratio: 2 / 1; overflow: hidden; position: relative;margin-bottom: var(--prpl-padding);">
				<div style="width: 100%; aspect-ratio: 1 / 1; border-radius: 100%; position: relative; background: radial-gradient(${ background } 0 ${ cutout }, transparent ${ cutout } 100%), conic-gradient(from ${ start }, ${ color } calc(${ maxDeg } * ${ value }), var(--prpl-color-gray-1) calc(${ maxDeg } * ${ value }) ${ maxDeg }, transparent ${ maxDeg }); text-align: center;">
					<span style="font-size: var(--prpl-font-size-small); position: absolute; top: 50%; color: var(--prpl-color-gray-5); width: 10%; text-align: center; left:0;">0</span>
					${ content }
					<span style="font-size: var(--prpl-font-size-small); position: absolute; top: 50%; color: var(--prpl-color-gray-5); width: 10%; text-align: center; right:0;">${ max }</span>
				</div>
			</div>
			`;
		}
	}
);
