<?php
/**
 * Popover base class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner;

/**
 * Popover base class.
 */
class Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Get a popover instance.
	 *
	 * @param string $id The popover ID.
	 *
	 * @return self
	 */
	public function the_popover( $id ) {
		$popover     = new self();
		$popover->id = $id;
		return $popover;
	}

	/**
	 * Render the triggering button.
	 *
	 * @param string $icon    The dashicon to use.
	 * @param string $content The content to use.
	 * @return void
	 */
	public function render_button( $icon, $content ) {
		?>
		<!-- The triggering button. -->
		<button
			class="prpl-info-icon"
			popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>"
			id="prpl-popover-<?php echo \esc_attr( $this->id ); ?>-trigger"
		>
			<?php if ( '' !== $icon ) : ?>
				<span class="dashicons dashicons-<?php echo \esc_attr( $icon ); ?>"></span>
			<?php endif; ?>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
		<?php
	}

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<div id="prpl-popover-<?php echo \esc_attr( $this->id ); ?>" class="prpl-popover" popover>
			<!-- The content. -->
			<?php \progress_planner()->the_view( 'popovers/' . $this->id . '.php' ); ?>

			<!-- The close button. -->
			<button
				class="prpl-popover-close"
				popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>"
				popovertargetaction="hide"
			>
				<span class="dashicons dashicons-no-alt"></span>
				<span class="screen-reader-text"><?php \esc_html_e( 'Close', 'progress-planner' ); ?>
			</button>

		</div>
		<?php
	}
}
