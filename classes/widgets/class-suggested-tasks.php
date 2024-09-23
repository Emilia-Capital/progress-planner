<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

use Progress_Planner\Suggested_Tasks as Root_Suggested_Tasks;

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
		?>
		<h2 class="prpl-widget-title">
			<?php esc_html_e( 'Suggested tasks', 'progress-planner' ); ?>
		</h2>

		<ul style="display:none">
			<?php include PROGRESS_PLANNER_DIR . '/views/suggested-todo-item.php'; ?>
		</ul>
		<ul class="prpl-suggested-todos-list priority-high"></ul>
		<ul class="prpl-suggested-todos-list priority-medium"></ul>
		<ul class="prpl-suggested-todos-list priority-low"></ul>
		<?php
	}
}
