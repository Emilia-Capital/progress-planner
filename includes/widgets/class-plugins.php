<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner\Widgets;

use ProgressPlanner\Widgets\Widget;

/**
 * Plugins Widget.
 */
final class Plugins extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'plugins';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {

		$plugins_count = count( \get_plugins() );

		// Get the number of pending updates.
		$pending_plugin_updates = \wp_get_update_data()['counts']['plugins'];
		?>

		<div class="two-col narrow<?php echo $pending_plugin_updates ? ' pending-updates' : ''; ?>">
			<div class="counter-big-wrapper">
				<span class="counter-big-number">
					<?php echo \esc_html( \number_format_i18n( $plugins_count ) ); ?>
				</span>
				<span class="counter-big-text">
					<?php \esc_html_e( 'plugins', 'progress-planner' ); ?>
				</span>
			</div>
			<div class="prpl-widget-content">
				<p>
					<?php if ( 0 === $pending_plugin_updates ) : ?>
						<?php \esc_html_e( 'Well done! All your plugins are up to date.', 'progress-planner' ); ?>
					<?php else : ?>
						<p>
							<?php
							printf(
								/* translators: %1$s: number of plugins. %2$s: Total number of pending updates. */
								\esc_html__( 'You have %1$s plugins installed. There are %2$s pending updates.', 'progress-planner' ),
								\esc_html( \number_format_i18n( $plugins_count ) ),
								'<span class="accent">' . \esc_html( \number_format_i18n( $pending_plugin_updates ) ) . '</span>'
							);
							?>
						</p>
						<p>
							<a href="<?php echo \esc_url( \admin_url( 'update-core.php' ) ); ?>" style="font-weight: 600; color:var(--prpl-color-gray-6);">
								<?php \esc_html_e( 'Update your plugins now.', 'progress-planner' ); ?>
							</a>
						</p>
					<?php endif; ?>
				</p>
			</div>
		</div>
		<?php
	}
}