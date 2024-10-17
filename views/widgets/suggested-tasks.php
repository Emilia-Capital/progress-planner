<?php
/**
 * Widget view.
 *
 * @package Progress_Planner
 */

use Progress_Planner\Suggested_Tasks;

?>
<h2 class="prpl-widget-title">
	<?php \esc_html_e( 'Suggested tasks', 'progress-planner' ); ?>
</h2>

<ul style="display:none">
	<?php
	/**
	 * Allow filtering the template for suggested tasks items.
	 *
	 * @param string $template_file The template file path.
	 */
	$template_file = \apply_filters( 'progress_planner_suggested_todo_item_template', PROGRESS_PLANNER_DIR . '/views/suggested-tasks-item.php' );
	include $template_file; // phpcs:ignore PEAR.Files.IncludingFile.UseRequire
	?>
</ul>
<ul class="prpl-suggested-todos-list"></ul>
<?php if ( Suggested_Tasks::maybe_celebrate_tasks() ) : ?>
	<script>
		alert( '<?php echo \esc_js( \esc_html__( 'Congratulations! You have completed all suggested tasks for this week.', 'progress-planner' ) ); ?>' );
	</script>
<?php endif; ?>
