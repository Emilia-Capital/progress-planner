<?php
/**
 * Progress_Planner widget.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * Published Content Widget.
 */
final class ToDo extends Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'todo';

	/**
	 * Render the widget content.
	 *
	 * @return void
	 */
	protected function the_content() {
		?>
		<h2 class="prpl-widget-title">
			<?php \esc_html_e( 'TODO list', 'progress-planner' ); ?>
		</h2>

		<ul id="todo-list" class="prpl-todo-list"></ul>

		<form id="create-todo-item">
			<input type="text" id="new-todo-content" placeholder="Add a new task" required />
			<button type="submit" title="<?php esc_html_e( 'Add', 'progress-planner' ); ?>">
				<span class="dashicons dashicons-plus-alt2"></span>
			</button>
		</form>
		<?php
	}
}
