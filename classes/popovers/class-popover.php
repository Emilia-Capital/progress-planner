<?php
/**
 * Popover base class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Popovers;

/**
 * Popover base class.
 */
abstract class Popover {

	/**
	 * The popover ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Render the triggering button.
	 *
	 * @return void
	 */
	public function render_button() {
		?>
		<!-- The triggering button. -->
		<button class="prpl-info-icon" popovertarget="prpl-popover-<?php echo \esc_attr( $this->id ); ?>">
			<span class="dashicons dashicons-info-outline"></span>
			<span class="screen-reader-text"><?php \esc_html_e( 'More info', 'progress-planner' ); ?>
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
		<?php $this->render_button(); ?>
		<div id="prpl-popover-<?php echo \esc_attr( $this->id ); ?>" class="prpl-popover" popover>
			<!-- The content. -->
			<?php $this->the_content(); ?>

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

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	abstract protected function the_content();
}
