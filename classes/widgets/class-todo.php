<?php // phpcs:disable Generic.Commenting.Todo
/**
 * A widget class.
 *
 * @package Progress_Planner
 */

namespace Progress_Planner\Widgets;

/**
 * ToDo class.
 */
final class ToDo extends \Progress_Planner\Widget {

	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	protected $id = 'todo';

	/**
	 * Print the widget content.
	 *
	 * @return void
	 */
	public function print_content() {
		echo '<p>' . \esc_html__( 'Write down all the website maintenance tasks you want to get done!', 'progress-planner' ) . '</p>';
		$this->the_todo_list();
	}

	/**
	 * The TODO list.
	 *
	 * @return void
	 */
	public function the_todo_list() {
		?>
		<div id="todo-aria-live-region" aria-live="polite" style="position: absolute; left: -9999px;"></div>

		<ul id="todo-list" class="prpl-todo-list"></ul>

		<form id="create-todo-item">
			<input type="text" id="new-todo-content" placeholder="<?php \esc_attr_e( 'Add a new task', 'progress-planner' ); ?>" aria-label="<?php \esc_attr_e( 'Add a new task', 'progress-planner' ); ?>" required />
			<button type="submit" title="<?php \esc_attr_e( 'Add', 'progress-planner' ); ?>">
				<span class="dashicons dashicons-plus-alt2"></span>
			</button>
		</form>
		<?php
	}
}
// phpcs:enable Generic.Commenting.Todo
