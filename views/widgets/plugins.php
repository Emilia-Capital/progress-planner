<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

$plugins_count = count( \get_plugins() );

// Get the number of pending updates.
$pending_plugin_updates = \wp_get_update_data()['counts']['plugins'];
?>

<div class="two-col narrow<?php echo $pending_plugin_updates ? ' pending-updates' : ''; ?>">
	<?php $this->render_big_counter( (int) $plugins_count, __( 'plugins', 'progress-planner' ) ); ?>
	<div class="prpl-widget-content">
		<?php if ( 0 === $pending_plugin_updates ) : ?>
			<?php \esc_html_e( 'Well done! All your plugins are up to date.', 'progress-planner' ); ?>
		<?php else : ?>
			<?php
			printf(
				\esc_html(
					/* translators: %1$s: number of installed plugins. %2$s: Total number of pending updates. */
					\_n(
						'You have %1$s plugins installed. There is %2$s pending update.',
						'You have %1$s plugins installed. There are %2$s pending updates.',
						(int) $pending_plugin_updates,
						'progress-planner'
					)
				),
				\esc_html( \number_format_i18n( $plugins_count ) ),
				'<span class="accent">' . \esc_html( \number_format_i18n( $pending_plugin_updates ) ) . '</span>'
			);
			?>
			<p>
				<a href="<?php echo \esc_url( \admin_url( 'update-core.php' ) ); ?>" style="font-weight: 600; color:var(--prpl-color-gray-6);">
					<?php \esc_html_e( 'Update your plugins now.', 'progress-planner' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</div>
<?php
