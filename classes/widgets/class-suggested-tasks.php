<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Suggested_Tasks as Suggested_Tasks_Core;

/**
 * Published Content Widget.
 */
final class Suggested_Tasks extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'suggested-todo';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		\wp_enqueue_script( 'progress-planner-suggested-tasks' );
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
			$template_file = \apply_filters( 'progress_planner_suggested_todo_item_template', PROGRESS_PLANNER_DIR . '/views/suggested-todo-item.php' );
			include $template_file;
			?>
		</ul>
		<ul class="prpl-suggested-todos-list"></ul>
		<?php if ( Suggested_Tasks_Core::maybe_celebrate_tasks() ) : ?>
			<script>
				alert( '<?php echo \esc_js( \esc_html__( 'Congratulations! You have completed all suggested tasks for this week.', 'progress-planner' ) ); ?>' );
			</script>
		<?php endif; ?>
		<?php
	}
}
