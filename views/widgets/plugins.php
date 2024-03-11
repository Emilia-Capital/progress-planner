<?php
/**
 * ProgressPlanner widget.
 *
 * @package ProgressPlanner
 */

namespace ProgressPlanner;

$prpl_plugins_count = count( \get_plugins() );

// Get the number of pending updates.
$prpl_pending_plugin_updates = wp_get_update_data()['counts']['plugins'];
?>

<div class="prpl-top-counter-bottom-content">
	<div class="counter-big-wrapper">
		<span class="counter-big-number">
			<?php echo esc_html( number_format_i18n( $prpl_plugins_count ) ); ?>
		</span>
		<span class="counter-big-text">
			<?php esc_html_e( 'plugins', 'progress-planner' ); ?>
		</span>
	</div>
	<div class="prpl-widget-content">
		<p>
			<?php if ( 0 === $prpl_pending_plugin_updates ) : ?>
				<?php esc_html_e( 'Well done! All your plugins are up to date.', 'progress-planner' ); ?>
			<?php else : ?>
				<?php
				printf(
					/* translators: %1$s: number of plugins. %2$s: Total number of pending updates. */
					esc_html__( 'You have %1$s plugins installed. There are %2$s pending updates.', 'progress-planner' ),
					esc_html( number_format_i18n( $prpl_plugins_count ) ),
					esc_html( number_format_i18n( $prpl_pending_plugin_updates ) )
				);
				?>
			<?php endif; ?>
		</p>
	</div>
</div>
